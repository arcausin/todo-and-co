<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\NewTaskType;
use App\Form\EditTaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/taches', name: 'app_task_')]
class TaskController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('task/index.html.twig', [
                'tasks' => $taskRepository->findBy(['user' => $this->getUser()], ['is_done' => 'ASC', 'created_at' => 'ASC']),
                'tasksAnonym' => $taskRepository->findBy(['user' => null], ['is_done' => 'ASC', 'created_at' => 'ASC'])
            ]);
        } else {
            return $this->render('task/index.html.twig', [
                'tasks' => $taskRepository->findBy(['user' => $this->getUser()], ['is_done' => 'ASC', 'created_at' => 'ASC'])
            ]);
        }
    }

    #[Route('/ajouter', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $task = new Task();
        $form = $this->createForm(NewTaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setIsDone(false);
            $task->setCreatedAt(new \DateTimeImmutable());
            $task->setUser($this->getUser());
            
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('successTaskAdd', 'La tâche '.$task->getTitle().' a été ajoutée avec succès.');

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
            'addTask' => true
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || $this->getUser() !== $task->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(EditTaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('successTaskEdit', 'La tâche '.$task->getTitle().' a été modifiée avec succès.');

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
            'editTask' => true
        ]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'])]
    public function delete(Task $task, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || $this->getUser() !== $task->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }

        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('successTaskDelete', 'La tâche '.$task->getTitle().' a été supprimée avec succès.');

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }
}
