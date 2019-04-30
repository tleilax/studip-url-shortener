<?php
class RegisterCronjob extends Migration
{
    public function up()
    {
        require_once __DIR__ . '/../lib/Cronjob.php';
        $task = new URLShortener\Cronjob();
        $task_id = CronjobScheduler::registerTask($task);
        $schedule = CronjobScheduler::schedulePeriodic(
            $task_id,
            null, null, null, null, null,
            CronjobSchedule::PRIORITY_NORMAL,
            array_map(function ($v) {
                return $v['default'];
            }, URLShortener\Cronjob::getParameters())
        );
        $schedule->active = true;
        $schedule->store();
    }

    public function down()
    {
        $task_id = CronjobTask::findBySQL("filename LIKE '%URLShortener%Cronjob%'")->task_id;
        CronjobScheduler::unregisterTask($task_id);
    }
}
