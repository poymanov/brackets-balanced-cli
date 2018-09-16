<?php

namespace commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BracketsBalanced\BracketsBalanced;

class CheckCommand extends Command
{
    public function configure()
    {
        $this->setName('check')
             ->addArgument('filepath', InputArgument::REQUIRED, 'filepath')
             ->setDescription('Path to file with string');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $filepath = $input->getArgument('filepath');

        if (!is_file($filepath)) {
            $output->writeln($this->formatOutput('File not found', 'error'));
            return null;
        }

        $content = file_get_contents($filepath);
        $output->writeln($this->formatOutput("Check string: \n {$content}", 'info'));

        $balanced = new BracketsBalanced($content);
        $checkResult = $balanced->isBalanced();

        if ($checkResult) {
            $message = $this->formatOutput('Balanced', 'info');
        } else {
            $message = $this->formatErrors($balanced->errors);
        }

        $output->writeln($message);

        return true;

    }

    private function formatErrors($errors)
    {
        $text = '';

        if ($errors) {
            foreach ($errors as $error) {
                $text .= $error . PHP_EOL;
            }
        } else {
            $text = 'Not balanced';
        }

        return $this->formatOutput($text, 'error');
    }

    private function formatOutput($value, $type)
    {
        return "<{$type}>{$value}</{$type}>";
    }
}