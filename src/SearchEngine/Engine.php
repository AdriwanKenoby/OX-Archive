<?php

/**
 * Engine.php
 *
 * @copyright  2015 Christophe Demko christophe.demko@univ-lr.fr
 * @license    CeCILL-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 */

namespace SearchEngine;

/**
 * Engine class
 *
 * @since  0.0.1
 */
class Engine {

    /**
    * Elasticsearch conf
    */
    private $host;
    private $port;

    /**
     * Engine client
     */
    private $client;

    /**
     * Engine index
     */
    private $index;

    /**
     * Engine type
     */
    private $type;

    /**
     * Constructor
     */
    public function __construct($host, $port, $index, $type) {
        $this->host = $host;
        $this->port = $port;
        $this->index = $index;
        $this->type = $type;
    }

    /**
     * Get the client (lazy instantiation)
     *
     * @return  \Elasticsearch\Client
     */
    protected function getClient() {
        if (!isset($this->client)) {
            $this->client = \Elasticsearch\ClientBuilder::create()
            ->setHosts([$this->host.':'.$this->port])
            ->build();
        }

        return $this->client;
    }

    /**
     * Index a path
     *
     * @param   string  $path  The pathname
     *
     * @return  void
     */
    public function index($path, $extra = array()) {

        $doc = DocumentBuilder::create()->build($path);
        $meta = $doc->parse();

        if(!empty($extra)) {
            foreach ($extra as $key => $value) {
                $meta[$key] = $value;
            }
        }

        $params = ['index' => $this->index, 'type' => $this->type, 'id' => md5($meta['path']), 'body' => $meta];
        $this->getClient()->index($params);
    }

    /**
     * Search a query string
     *
     * @param   string  $query  The query string
     *
     * @return  void
     */
    public function search($query) {
        $params = ['index' => $this->index, 'type' => $this->type, 'q' => $query];
        $search = $this->getClient()->search($params);

        $result = [];

        foreach ($search['hits']['hits'] as $hit) {
            $result[] = ['score' => $hit['_score'], 'path' => $hit['_source']['path']];
        }

        return $result;
    }

    /**
     * Delete a path
     *
     * @param   string  $path  The pathname
     *
     * @return  void
     */
    public function delete($path) {
        $id = md5(realpath($path));
        $params = ['index' => $this->index, 'type' => $this->type, 'id' => $id];
        $this->getClient()->delete($params);
    }

}
