#!/usr/bin/env php
<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\Book;
use App\Repository\BookRepository;


#[AsCommand(
    name: 'app:parse',
    description: 'Parses remote JSON with books.',
    hidden: false
)]
class ParseCommand extends Command
{
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        // $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
        parent::__construct();
    }

    private function get_decoded_json(OutputInterface $output)
    {
        $filename = 'var/cache/books.json';
        $json = '';
        if (file_exists($filename)) {
            $json = file_get_contents($filename);
            $output->writeln('File read from cache');
            return json_decode($json);
        }

        $url = 'https://gitlab.com/prog-positron/test-app-vacancy/-/raw/master/books.json';
        $output->writeln('Downloading file from '.$url);
        $json = file_get_contents($url);
        if (!$json) {
            $output->writeln('Download ERROR');
            return FALSE;
        }

        $output->writeln('File downloaded');
        file_put_contents($filename, $json);
        $output->writeln('File written to cache');
        return json_decode($json);
    }

    private function log(OutputInterface $output, int $id, object $book, string $propertyName) {
        if (property_exists($book, $propertyName)) {
            $val = $book->$propertyName;
            $output->writeln("[$id][$propertyName] $val");
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $books = $this->get_decoded_json($output);
        if (!$books) {
            return Command::FAILURE;
        }

        $authors = array();
        foreach($books as $id=>$book) {
            $dbBook = new Book();

            $output->writeln("[$id][Title] $book->title");

            $dbBook->setTitle($book->title);
            // $dbBook->setIsbn($book->isbn);
            $dbBook->setPageCount($book->pageCount);
            $dbBook->setStatus($book->status);

            // $dbBook->setPublishedDate($book->pageCount);
            // $dbBook->setThumbnailUrl($book->pageCount);
            // $dbBook->setShortDescription($book->pageCount);
            // $dbBook->setLongDescription($book->pageCount);

            // $this->log($output, $id, $book, 'publishedDate');
            $this->log($output, $id, $book, 'shortDescription');
            $this->log($output, $id, $book, 'longDescription');
            $this->log($output, $id, $book, 'thumbnailUrl');
            // $output->writeln($id.': '.$book->authors);
            // $output->writeln($id.': '.$book->categories);
            $output->writeln("");
            $this->bookRepository->add($dbBook);
        }
        $this->bookRepository->flush();
        // var_dump($books);
        //print_r($books);
        
        return Command::SUCCESS;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }

    // protected function configure(): void
    // {
    //     $this
    //         ->setHelp('This command downloads JSON file from... and puts its content into database')
    //     ;
    // }
}