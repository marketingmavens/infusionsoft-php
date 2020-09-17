<?php

require_once('../vendor/autoload.php');
require_once('./helpers.php');

$infusionsoft = set_up_and_get_infusionsoft(__FILE__);

/**
 * @param \Infusionsoft\Infusionsoft $infusionsoft
 *
 * @return mixed
 */
function taskManager($infusionsoft)
{
    $tasks = $infusionsoft->tasks();

    // first, create a new task
    $task = $tasks->create([
        'title'       => 'Test Task',
        'description' => 'This is the task description'
    ]);

    // oops, we wanted a different title
    $task->title = 'Real Test Task';
    $task->save();

    return $task;
}

if ($infusionsoft->getToken()) {
    try {
        $task = taskManager($infusionsoft);

        var_dump($task);
    } catch (\Infusionsoft\InfusionsoftException $e) {
        die($e->getMessage());
    }
}