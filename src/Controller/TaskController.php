<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Entity\Task;
use App\Entity\Project;
use App\Repository\ProjectRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


class TaskController extends AbstractController
{
    /**
     * @Route("/project/{id}/tasks", methods={"GET"}, name="tasks")
     * 
     */
    public function Task(TaskRepository $taskRepository, int $id, ProjectRepository $projectRepository, Project $project): Response
    {
        

        $task = $project-> getTasks();

        return $this->render('task/index.html.twig', [
            'project_id'=> $project-> getId(),
            'tasks'=> $task,
            'project' => $project,
        ]);
    }

    /**
     * @Route("/project/{id}/tasks/add", methods={"GET","POST"}, name="task_add")
     * 
     */
    public function addTask(Project $project): Response
    {
        

        return $this->render('task/addtask.html.twig',[
            'project'=> $project,
            
        
        ]) ;
        
    }

    /**
     * @Route("/project/{id}/tasks/save", methods={"POST"}, name="task_add_save")
     */
    public function TasksAddSave(ManagerRegistry $doctrine, Request $request, Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $task = new Task();
        $task->setName($request->request->get('name'));
        $task->setDescription($request->request->get('description'));
        $task->setStartDate(new \DateTime($request->request->get('start_date')));
        $task->setEndDate(new \DateTime($request->request->get('end_date')));
        $task->setProject($project);

        
        $entityManager->persist($task);
        $entityManager->flush();
        
        

        return $this->redirectToRoute('tasks',["id" => $project->getId()]);
    }

  
    /**
     * @Route("/project/{project_id}/edittasks/{task_id}", name="tasks_edit")
     * @ParamConverter("project", options={"mapping": {"project_id": "id"}})
     * @ParamConverter("task", options={"mapping": {"task_id": "id"}})
     */
    public function editTask(Project $project, Task $task): Response
    {
        
        return $this->render('task/edittask.html.twig',[
            'project' => $project,
            'task' => $task
        ]) ;
    }

      /**
     * @Route("/project/{project_id}/edit/{task_id}/save", methods={"POST"}, name="task_edit_save")
     * @ParamConverter("project", options={"mapping": {"project_id": "id"}})
     * @ParamConverter("task", options={"mapping": {"task_id": "id"}})
     */
    public function taskEditSave(ManagerRegistry $doctrine, Request $request, Task $task, Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $task->setName($request->request->get('name'));
        $task->setDescription($request->request->get('description'));
        $task->setStartDate(new \DateTime($request->request->get('start_date')));
        $task->setEndDate(new \DateTime($request->request->get('end_date')));

        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('tasks', ["id" => $project->getId()]);
    }
    /**
     * @Route("/project/{project_id}/deleteTask/{task_id}", methods={"GET"}, name="task_delete")
     * @ParamConverter("project", options={"mapping": {"project_id": "id"}})
     * @ParamConverter("task", options={"mapping": {"task_id": "id"}})
     */
    public function taskDelete(ManagerRegistry $doctrine, Task $task, Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('tasks', ["id" => $project->getId()]);
    }
}
