<?php
namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Yaml\Yaml;

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
            ->addOption('url','u' ,InputOption::VALUE_REQUIRED, 'Pass the URL')
						->addOption('path','p',InputOption::VALUE_OPTIONAL, 'Write my output file to.. Defaults to /tmp/' )
						->addOption('outputTo','o',InputOption::VALUE_OPTIONAL, 'Name of the output file .. Defaults to output.csv')
            ->addOption('email','e',InputOption::VALUE_OPTIONAL, 'Email this report to ...')
            ->addOption('outputFormat','f' ,InputOption::VALUE_OPTIONAL, 'CSV/YAML. Default is CSV');
    }

	protected function execute(InputInterface $input, OutputInterface $output)
    {
      //$downloader   = new CurlDownloader('https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1/orders.jsonl', '/tmp/');
		   $path = $input->getOption('path');
			 $path =isset($path) ? $path :'/tmp/';
			 $outputFormat = $input->getOption('outputFormat');
			 $outputFormat = isset($outputFormat) ? $outputFormat:'CSV';
			 $outputFile = $input->getOption('outputTo');
			 $outputFile =isset($outputFile) ? $outputFile : 'output';



      $downloader = new CurlDownloader($input->getOption('url'), $path);
			$outputWriter  = OutputWriterFactory::getWriter($outputFormat);
			$errorFile    =  OutputWriterFactory::getWriter("CSV"); //Will write only csv


      $outputWriter->setFileName($path."/".$outputFile);
      $errorFile->setFilename('/tmp/errors.csv');
      $parser        = new ResponseParser();
      try {
          $fetchedFile = $downloader->fetch();
          $parser->setInputFileName($fetchedFile);
          $parser->parser(array(
               'inputFormat'   =>  'JSONL',
               'errorFile'     =>  $errorFile,
               'outputWriter'  =>  $outputWriter,
							 'outputFormat'  => $outputFormat
          ));

      } catch (\Exception $exception) {
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
