<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('ox-archivage', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);
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
