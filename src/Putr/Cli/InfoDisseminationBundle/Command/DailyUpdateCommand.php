<?php

namespace Putr\Cli\InfoDisseminationBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;


class DailyUpdateCommand extends Command {

	protected function configure() {
		$this
			->setName('info:daily')
			->setDescription('Retrives data, compiles an email and sends it')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

      $this->container = $this->getApplication()->getKernel()->getContainer();
      $this->logger    = $this->container->get('logger');
      $forum           = $this->container->get('info.forum');
      $wiki            = $this->container->get('info.mediawiki');
      $wordpress       = $this->container->get('info.wordpress');

      $this->logger->info("Runing daily update");

      $forumData = $forum->getDaily();
      $forumData = $forum->remapSimple($forumData);

      $wikiData = $wiki->getDaily();
      $wikiData = $wiki->remapItems($wikiData);

      $wordpressData = $wordpress->getDaily();
      $wordpressData = $wordpress->remapItems($wordpressData);

      if (empty($forumData) && empty($wikiData) && empty($wordpressData)) {
         $this->logger->info("Nothing new to report :/");
         return;
      }

      $this->logger->info(sprintf("New stuff to report"), array(
         'forum'     => count($forumData),
         'wiki'      => count($wikiData),
         'wordpress' => count($wordpressData)
         ));

      $message = \Swift_Message::newInstance()
          ->setSubject('Nova dogajanja!')
          ->setFrom($this->container->getParameter('info_dissemination.system_email'))
          ->setTo($this->container->getParameter('info_dissemination.to_email'))
          ->setBody($this->container->get('templating')->render('PutrInfoDisseminationBundle:Mail:dailyInfo.txt.twig', array(
                  'forum' => $forumData,
                  'wiki' => $wikiData,
                  'wordpress' => $wordpressData
          )))
      ;

      $this->container->get('mailer')->send($message);

      // 
      // Flushing emails
      //
      $transport = $this->container->get('mailer')->getTransport();
      if (!$transport instanceof \Swift_Transport_SpoolTransport) {
            return;
      }

      $spool = $transport->getSpool();
      if (!$spool instanceof \Swift_MemorySpool) {
            return;
      }

      $spool->flushQueue($this->container->get('swiftmailer.transport.real'));


   }
}