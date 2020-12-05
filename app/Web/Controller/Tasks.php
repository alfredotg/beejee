<?php

namespace BeeJee\Web\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use BeeJee\Web\Controller;
use BeeJee\Model\Task;
use BeeJee\Form;
use BeeJee\AuthInterface;
use League\Route\Http\Exception\NotFoundException;

class Tasks extends Controller
{
    const PAGE_SIZE = 3;

    public function list(ServerRequestInterface $request): ResponseInterface
    {
        $tasks = Task::query();
        $count = $tasks->count();

        $query = $request->getQueryParams();
        $page = max(0, intval($query['page'] ?? null) - 1);

        $sort = $query['sort'] ?? null;
        if (count($chunks = explode('-', $sort)) == 2) {
            list($sort_field, $sort_direction) = $chunks;
            if (in_array($sort_field, ['username', 'email', 'status']) &&
               in_array($sort_direction, ['asc', 'desc'])) {
                $tasks->orderBy($sort_field, $sort_direction);
            }
        }

        $pager_query = $query;
        unset($pager_query['page']);

        $sort_query = $query;
        unset($sort_query['sort']);

        $tasks->offset(self::PAGE_SIZE * $page);
        $tasks->limit(self::PAGE_SIZE);
        $tasks = $tasks->get();

        $request_path = ltrim($request->getUri()->getPath(), '/');
        return $this->viewResponse('tasks/list.html', [
            'tasks' => $tasks,
            'pages' => max(1, ceil($count / self::PAGE_SIZE)),
            'current_page' => $page + 1,
            'pager_url' => $request_path . '?' . http_build_query($pager_query),
            'sort_url' => $request_path . '?' . http_build_query($sort_query),
            'current_sort' => $sort
        ]);
    }

    public function add(ServerRequestInterface $request): ResponseInterface
    {
        $task = new Task;
        $task->status = Task::STATUS_NEW;

        $errors = [];
        if ($request->getMethod() == 'POST') {
            $form = new Form\Task;
            $errors = $form ->bind($task, $request->getParsedBody());
            if (!count($errors)) {
                $task->save();
                return $this->redirect('/');
            }
        }
        return $this->viewResponse('tasks/form.html', [
            'request' => $request,
            'task' => $task,
            'errors' => $errors
        ]);
    }
    
    public function edit(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $this->di->get(AuthInterface::class)->require();

        $task = Task::firstWhere('id', $args['id']);
        if (!$task) {
            throw new NotFoundException();
        }

        $errors = [];
        if ($request->getMethod() == 'POST') {
            $form = new Form\TaskEdit;
            $errors = $form ->bind($task, $request->getParsedBody());
            $task->description_changed = $task->isDirty('description') ? 1 : 0;
            $task->status = intval($task->status) ?: Task::STATUS_NEW;
            if (!count($errors)) {
                $task->save();
                return $this->redirect('/');
            }
        }
        return $this->viewResponse('tasks/form.html', [
            'is_edit' => true,
            'request' => $request,
            'task' => $task,
            'errors' => $errors
        ]);
    }
}
