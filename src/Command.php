<?php
namespace Packagix;
class Command
{
    /**
     * @throws \RuntimeException
     * @throws \PHPUnit\Framework\Exception
     * @throws \InvalidArgumentException
     */
    public static function main(bool $exit = true): int
    {
        $command = new static;

        return $command->run($_SERVER['argv'], $exit);
    }

    /**
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    public function run(array $argv, bool $exit = true): int
    {
        /*
        $this->handleArguments($argv);

        $runner = $this->createRunner();

        if ($this->arguments['test'] instanceof Test) {
            $suite = $this->arguments['test'];
        } else {
            $suite = $runner->getTest(
                $this->arguments['test'],
                $this->arguments['testFile'],
                $this->arguments['testSuffixes']
            );
        }

        if ($this->arguments['listGroups']) {
            return $this->handleListGroups($suite, $exit);
        }

        if ($this->arguments['listSuites']) {
            return $this->handleListSuites($exit);
        }

        if ($this->arguments['listTests']) {
            return $this->handleListTests($suite, $exit);
        }

        if ($this->arguments['listTestsXml']) {
            return $this->handleListTestsXml($suite, $this->arguments['listTestsXml'], $exit);
        }

        unset($this->arguments['test'], $this->arguments['testFile']);

        try {
            $result = $runner->doRun($suite, $this->arguments, $exit);
        } catch (Exception $e) {
            print $e->getMessage() . \PHP_EOL;
        }

        $return = TestRunner::FAILURE_EXIT;

        if (isset($result) && $result->wasSuccessful()) {
            $return = TestRunner::SUCCESS_EXIT;
        } elseif (!isset($result) || $result->errorCount() > 0) {
            $return = TestRunner::EXCEPTION_EXIT;
        }

        if ($exit) {
            exit($return);
        }

        return $return;
        */
    }

}