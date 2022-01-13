<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Entity\Task;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProjectController extends AbstractController
{
    /**
     * @Route("/project", name="projects")
     */
    public function projects(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/project/add", methods={"GET","POST"}, name="project_add")
     * 
     */
    public function addProject(ProjectRepository $projectRepository,LoggerInterface $logger, SessionInterface $session): Response
    {
       

        return $this->render('project/addproject.html.twig') ;
    }

    /**
     * @Route("/project/add/save", methods={"POST"}, name="project_add_save")
     */
    public function projectsAddSave(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator): Response
    {
       

        $entityManager = $doctrine->getManager();

        $format = 'Y-m-d';

        $project = new Project();
        $project->setName($request->request->get('name'));
        $project->setDescription($request->request->get('description'));
        $project->setStartDateStr($request->request->get('start_date'));
        $project->setEndDateStr($request->request->get('end_date'));
        

        $errors = $validator->validate($project);

        if (count($errors) > 0) {

            //$errorsString = (string) $errors;

            $this-> addFlash('errors',$errors);

            return $this-> redirectToRoute('project_add'); 
        }

        $project->setStartDate(\DateTime::createFromFormat($format, $request->request->get('start_date')));
        $project->setEndDate(\DateTime::createFromFormat($format, $request->request->get('end_date')));
    
        
        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('projects');
    }

    /**
     * @Route("/project/edit/{id}", methods={"GET","POST"}, name="project_edit")
     * 
     */
    public function editProject(ProjectRepository $projectRepository, Project $project): Response
    {
        $users = $project->getUsers();
        
        return $this->render('project/editproject.html.twig',[
            'projectId' => $project->getId(),
            'project' => $project,
            'users' => $users,
        ]) ;
    }

     /**
     * @Route("/project/save/{id}", methods={"POST"}, name="save_project")
     */
    public function saveProject(Request $request, ManagerRegistry $doctrine, Project $project):Response{

        $format = 'Y-m-d';


        $project -> setName($request->request->get('name'));
        $project -> setDescription($request->request->get('description'));
        $project -> setStartDate(\DateTime::createFromFormat($format,$request->request->get('start_date')));
        $project -> setEndDate(\DateTime::createFromFormat($format,$request->request->get('end_date')));

        
        $entityManager = $doctrine->getManager();

        $entityManager->persist($project);
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        
        return $this->redirectToRoute('projects');
    }

     /**
     * @Route("/project/delete/{id}", methods={"GET", "DELETE"}, name="project_delete")
     * 
     */
    public function deleteProject(Request $request, Project $project, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($project);
        $entityManager->flush();

        return $this->redirectToRoute('projects');
    }
    
    /**
     * @Route("/project/{id}/user", methods={"GET"}, name="projects_addUser")
     */
    public function projectsAddUser(UserRepository $userRepository, Project $project): Response
    {
        $users = $userRepository->findAll();

        $projectUsers = $project->getUsers();

        return $this->render('project/addUser.html.twig', [
            'project' => $project,
            'users' => $users,
            'projectUsers' => $projectUsers,
        ]);
    }

    /**
     * @Route("/project/{id}/user/save", methods={"POST"}, name="projects_addUser_save")
     */
    public function projectsAddUserSave(LoggerInterface $logger, ManagerRegistry $doctrine, UserRepository $userRepository, Request $request, Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $project->clearUsers();

        $listIdUser = $request->request->get('user_id', []);

        $logger->debug("valeur userId", ["userId"=>$listIdUser]);
        
        foreach($listIdUser as $userId) {
            $user = $userRepository->find($userId);
            $project->addUser($user);
        }

        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('projects');
    }

}