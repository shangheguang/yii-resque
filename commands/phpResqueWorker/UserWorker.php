<?php
/**
* Worker for ClassWorker
*/
class Worker_UserWorker
{
    public function setUp()
    {
        # Set up environment for this job
        echo "ueser_Set up\n";
    }

    public function perform()
    {
        # Run task
        echo "ueser_Run\n";
    }

    public function tearDown()
    {
        # Remove environment for this job
        echo "ueser_Tear down\n";
    }
    
}