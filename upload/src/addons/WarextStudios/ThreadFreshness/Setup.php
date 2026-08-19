<?php

namespace WarextStudios\ThreadFreshness;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1(): void
    {
        $this->schemaManager()->createTable('xf_wrxt_thread_freshness_state', function(Create $table)
        {
            $table->addColumn('thread_id', 'int')->unsigned();
            $table->addColumn('status', 'varchar', 32)->setDefault('unverified');
            $table->addColumn('score', 'decimal', '8,4')->setDefault(0);
            $table->addColumn('positive_weight', 'decimal', '10,4')->setDefault(0);
            $table->addColumn('negative_weight', 'decimal', '10,4')->setDefault(0);
            $table->addColumn('vote_count', 'int')->unsigned()->setDefault(0);
            $table->addColumn('positive_count', 'int')->unsigned()->setDefault(0);
            $table->addColumn('negative_count', 'int')->unsigned()->setDefault(0);
            $table->addColumn('last_vote_date', 'int')->unsigned()->setDefault(0);
            $table->addColumn('last_calculated_date', 'int')->unsigned()->setDefault(0);
            $table->addColumn('last_verified_date', 'int')->unsigned()->setDefault(0);
            $table->addColumn('moderator_status', 'varchar', 32)->setDefault('');
            $table->addColumn('moderator_user_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('moderator_date', 'int')->unsigned()->setDefault(0);
            $table->addPrimaryKey('thread_id');
            $table->addKey('status');
            $table->addKey('last_calculated_date');
        });
    }

    public function installStep2(): void
    {
        $this->schemaManager()->createTable('xf_wrxt_thread_freshness_vote', function(Create $table)
        {
            $table->addColumn('vote_id', 'int')->unsigned()->autoIncrement();
            $table->addColumn('thread_id', 'int')->unsigned();
            $table->addColumn('user_id', 'int')->unsigned();
            $table->addColumn('vote', 'tinyint');
            $table->addColumn('reason', 'varchar', 64)->setDefault('');
            $table->addColumn('version', 'varchar', 100)->setDefault('');
            $table->addColumn('message', 'varchar', 500)->setDefault('');
            $table->addColumn('vote_date', 'int')->unsigned();
            $table->addColumn('updated_date', 'int')->unsigned()->setDefault(0);
            $table->addPrimaryKey('vote_id');
            $table->addUniqueKey(['thread_id', 'user_id'], 'thread_user');
            $table->addKey(['thread_id', 'vote_date'], 'thread_date');
            $table->addKey(['user_id', 'vote_date'], 'user_date');
        });
    }

    public function installStep3(): void
    {
        $this->schemaManager()->createTable('xf_wrxt_thread_freshness_log', function(Create $table)
        {
            $table->addColumn('log_id', 'int')->unsigned()->autoIncrement();
            $table->addColumn('thread_id', 'int')->unsigned();
            $table->addColumn('old_status', 'varchar', 32)->setDefault('');
            $table->addColumn('new_status', 'varchar', 32)->setDefault('');
            $table->addColumn('trigger_type', 'varchar', 32)->setDefault('system');
            $table->addColumn('user_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('log_date', 'int')->unsigned();
            $table->addPrimaryKey('log_id');
            $table->addKey(['thread_id', 'log_date'], 'thread_date');
        });
    }

    public function uninstallStep1(): void
    {
        $this->schemaManager()->dropTable('xf_wrxt_thread_freshness_log');
        $this->schemaManager()->dropTable('xf_wrxt_thread_freshness_vote');
        $this->schemaManager()->dropTable('xf_wrxt_thread_freshness_state');
    }
}
