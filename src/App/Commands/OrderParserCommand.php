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
            ->addOption('url','u' ,InputArgument::REQUIRED, 'Pass the URL')
						->addOption('path','p',InputArgument::OPTIONAL, 'Write my output file to.. Defaults to /tmp/' )
						->addOption('output','o',InputArgument::OPTIONAL, 'Name of the output file .. Defaults to output.csv')
            ->addOption('email','e',InputArgument::OPTIONAL, 'Email this report to ...')
            ->addOption('outputFormat','f' ,InputArgument::OPTIONAL, 'CSV/YAML. Default is CSV');
    }

	protected function execute(InputInterface $input, OutputInterface $output)
    {
      //$downloader   = new CurlDownloader('https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1/orders.jsonl', '/tmp/');
		   $path = $input->getArgument('path');
			 $path =isset($path) ? $path :'/tmp/';
			 $outputFormat = $input->getArgument('outputFormat');
			 $outputFormat = isset($outputFormat) ? $outputFormat:'CSV';


      $downloader = new CurlDownloader($input->getArgument('url', $path));
      $errorFile    =  OutputWriterFactory::getWriter("CSV"); //Will write only csv
      $outputWriter  = OutputWriterFactory::getWriter($outputFormat);

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
