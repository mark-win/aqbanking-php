<?php

namespace AqBanking\Command;

use AqBanking\Account;
use AqBanking\ContextFile;

class RequestCommand extends AbstractCommand
{
    /**
     * @var Account
     */
    private $account;

    /**
     * @var ContextFile
     */
    private $contextFile;

    /**
     * @var string
     */
    private $pathToPinList;

    /**
     * @param Account $account
     * @param ContextFile $contextFile
     * @param string $pathToPinList
     */
    public function __construct(Account $account, ContextFile $contextFile, $pathToPinList)
    {
        $this->account = $account;
        $this->contextFile = $contextFile;
        $this->pathToPinList = $pathToPinList;
    }

    /**
     * @param \DateTime $fromDate
     */
    public function execute(\DateTime $fromDate = null)
    {
        $shellCommand = $this->getShellCommand($fromDate);
        $this->getShellCommandExecutor()->execute($shellCommand);
    }

    /**
     * @param \DateTime $fromDate
     * @return string
     */
    private function getShellCommand(\DateTime $fromDate = null)
    {
        $shellCommand =
            "aqbanking-cli"
            . " --noninteractive"
            . " --acceptvalidcerts"
            . " --pinfile=" . $this->pathToPinList
            . " request"
            . " --bank=" . $this->account->getBankCode()
            . " --account=" . $this->account->getAccountNumber()
            . " --ctxfile=" . $this->contextFile->getPath()
            . " --transactions"
            . " --balance"
            . " --sto"     // standing orders
            . " --dated"   // dated transfers
        ;

        if (null !== $fromDate) {
            $shellCommand .= " --fromdate=" . $fromDate->format('Ymd');
        }

        return $shellCommand;
    }
}