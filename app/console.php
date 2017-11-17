<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

// Ici on cree une nouvelle application en console independante de notre interface web
// On pourrais en particulier imaginer une commande de purge
$console = new Application('ox-archive', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);
// Indexation de document dans elasticsearch
// TODO add Array param to index extra field in elasticsearch
$console
    ->register('archive:document:index')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->addArgument('document', InputArgument::REQUIRED, 'document to index')
    ->setDescription('index document in elasticsearch')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $app['search_engine']->index($input->getArgument('document'));
    })
;
// Recherche de document, il faut echapper les caractere speciaux
$console
    ->register('archive:document:search')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->addArgument('query', InputArgument::REQUIRED, 'query to search document')
    ->setDescription('search a document in elasticsearch')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $result = $app['search_engine']->search($input->getArgument('query'));
        foreach ($result as $hit) {
            $output->writeln(sprintf("%0.8f\t%s\n", $hit['score'], $hit['path']));
        }
    })
;
// Supprimer un document de moteur d'indexation, ne supprime pas le document sur le systeme de fichier
$console
    ->register('archive:document:delete')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->addArgument('document', InputArgument::REQUIRED, 'document to delete')
    ->setDescription('delete a document from elasticsearch')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $result = $app['search_engine']->delete($input->getArgument('document'));
    })
;

// generation des classes FHIR
$console
    ->register('archive:fhir:generate-classes')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->setDescription('generate fhir classes')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $xsdPath = __DIR__.'/../fhir-codegen-xsd/';
        $generator = new \DCarbone\PHPFHIR\ClassGenerator\Generator($xsdPath);
        $generator->generate();
    })
;

return $console;
