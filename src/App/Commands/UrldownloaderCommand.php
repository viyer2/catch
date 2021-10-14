<?php
namespace Console\App\Commands;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
 
class UrldownloaderCommand extends Command
{
	protected function configure()
    {
        $this->setName('url-download')
            ->setDescription('Downloads a file from a given URL')
            ->setHelp('Downloads a file from agiven URL')
            ->addArgument('url', InputArgument::REQUIRED, 'Pass the URL');
    }
 
	protected function execute(InputInterface $input, OutputInterface $output)
    {
      $curlHandler = curl_init($input->getArgument('url'));

	  $destinationDir = __DIR__.'/../';

	  $output->writeln(sprintf('Hello World!, %s',$destinationDir));
	  
      return 1; 
    }

}
