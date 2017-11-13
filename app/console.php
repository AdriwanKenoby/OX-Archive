<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use SearchEngine\EngineBuilder;

$console = new Application('ox-archivage', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);
$console
    ->register('archivage:document:index')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->addArgument('document', InputArgument::REQUIRED, 'document to index')
    ->setDescription('index document in elasticsearch')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        EngineBuilder::create()->build()->index($input->getArgument('document'));
    })
;

$console
    ->register('archivage:document:search')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->addArgument('query', InputArgument::REQUIRED, 'query to search document')
    ->setDescription('search a document in elasticsearch')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $result = EngineBuilder::create()->build()->search($input->getArgument('query'));
        foreach ($result as $hit) {
            $output->writeln(sprintf("%0.8f\t%s\n", $hit['score'], $hit['path']));
        }
    })
;

$console
    ->register('archivage:document:delete')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->addArgument('document', InputArgument::REQUIRED, 'document to delete')
    ->setDescription('delete a document from elasticsearch')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $result = EngineBuilder::create()->build()->delete($input->getArgument('document'));
    })
;

$console
    ->register('archivage:fhir:generate-classes')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->setDescription('generate fhir classes')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $xsdPath = __DIR__.'/../fhir-codegen-xsd/';
        $generator = new \DCarbone\PHPFHIR\ClassGenerator\Generator($xsdPath);
        $generator->generate();
    })
;

return $console;
