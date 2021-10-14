<?php
namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;

use Console\App\Classes\CurlDownloader;
use Console\App\Classes\OutputWriterFactory;
use Console\App\Classes\ResponseParser;


class OrderParserCommand extends Command
{
	protected function configure()
    {
        $this->setName('order-parser')
            ->setDescription('Downloads a JSON file from a given URL and parses it and produces output')
            ->setHelp('Downloads a file from agiven URL')
            ->addArgument('url', InputArgument::REQUIRED, 'Pass the URL')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email this report to ...')
            ->addArgument('outputFormat', InputArgument::OPTIONAL, 'CSV, XML, YAML. Default is CSV');
    }

	protected function execute(InputInterface $input, OutputInterface $output)
    {
      //$downloader   = new CurlDownloader('https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1/orders.jsonl', '/tmp/');
      $downloader = new CurlDownloader($input->getArgument('url'),'/tmp/');
      $errorFile    =  OutputWriterFactory::getWriter("CSV");
      $outputWriter  = OutputWriterFactory::getWriter("CSV");

      $outputWriter->setFileName('/tmp/output.csv');
      $errorFile->setFilename('/tmp/errors.csv');
      $parser        = new ResponseParser();
      try {
          $fetchedFile = $downloader->fetch();
          $parser->setInputFileName($fetchedFile);
          $parser->parser(array(
               'inputFormat'   =>  'CSV',
               'errorFile'     =>  $errorFile,
               'outputWriter'  =>  $outputWriter

          ));

      } catch (Exception $exception) {
          echo $exception->getMessage();
      }
      finally {
        $outputWriter->closeFileHandle();
        $errorFile->closeFileHandle();
        $output->writeln(sprintf('Order Processing! , %s',"Finished"));
				return 1;
      }
    }

}
