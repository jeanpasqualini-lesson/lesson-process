<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 3/12/15
 * Time: 3:47 PM
 */

namespace Test;


use Interfaces\TestInterface;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Touki\ConsoleColors\Formatter;

class MainTest implements TestInterface {

    private $consoleFormatter;

    public function __construct()
    {
        $this->consoleFormatter = new Formatter();
    }

    public function runTest()
    {
        $tests = array("one", "two", "three", "foor", "five", "six", "sept", "huit", "neuf", "dix");

        foreach($tests as $test)
        {
            echo $this->consoleFormatter->fromName(
                " ::: ".$test." :::".PHP_EOL,
                "red"
            );

            $this->{"test".ucfirst($test)}();
        }

    }

    public function testOne()
    {
        $process = new Process("ls -lsa");

        $process->run();

        if(!$process->isSuccessful())
        {
            throw new \RuntimeException($process->getErrorOutput());
        }

        echo $process->getOutput();
    }

    public function testTwo()
    {
        $process = new Process("lsa -lsa");

        try {
            $process->mustRun();
        }
        catch(ProcessFailedException $e)
        {
            echo $e->getMessage();

            return;
        }

        echo $process->getOutput();
    }

    public function testThree()
    {
        $process = new Process("ls -lsa");

        $process->run(function($type, $buffer)
        {
           if(Process::ERR === $type)
           {
               echo $this->consoleFormatter->fromName("ERR > ".$buffer, "red");
           }
           else
           {
               echo $this->consoleFormatter->fromName("OUT > ".$buffer, "green");
           }

           echo PHP_EOL;
        });

    }

    public function testFoor()
    {
        $process = new Process("ls -lsa");

        $process->start();

        while($process->isRunning())
        {
            echo $this->consoleFormatter->fromName("WAITING 100ms", "blue");

            echo PHP_EOL;

            usleep(100);
        }

        echo $this->consoleFormatter->fromName($process->getOutput(), "green");
    }

    public function testFive()
    {
        $process = new Process("ls -lsa");

        $process->start();

        $process->stop(3, SIGINT);
    }

    public function testSix()
    {
        $process = new PhpProcess("<?php echo 'Hello World'; ?>");

        $process->run();

        echo $process->getOutput();
    }

    public function testSept()
    {
        $builder = new ProcessBuilder();

        $builder
            ->setPrefix("ls")
            ->setArguments(array("-lsa"))
        ;


        $process = $builder->getProcess();

        $process->run();

        echo $process->getOutput();
    }

    public function testHuit()
    {
        $process = new Process("ls -lsa");

        //$process->setTimeout(1);

        $process->run();
    }

    public function testNeuf()
    {
        $process = new Process("ls -lsa");

        $process->setTimeout(1);

        $process->start();

        echo "pid : ".$process->getPid().PHP_EOL;

        try {

            while($process->isRunning())
            {
                $process->checkTimeout();

                echo "check".PHP_EOL;

                usleep(500);
            }

        }
        catch(ProcessTimedOutException $e)
        {
            echo "timeout : ".$e->getMessage().PHP_EOL;

            $e->getProcess()->signal(SIGKILL);
        }
    }

    public function testDix()
    {
        $process = new Process("ls -lsa");

        $process->disableOutput();

        $process->run();

        try
        {
            echo $process->getOutput();
        }
        catch(LogicException $e)
        {
            echo "output disabled : ".$e->getMessage().PHP_EOL;;
        }

    }

}