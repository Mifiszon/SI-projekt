<?php
/**
 * User Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangeNicknameType;
use App\Form\Type\Privileges\PromoteType;
use App\Form\Type\Privileges\RevokeType;
use App\Form\Type\RegistrationFormType;
use App\Form\Type\UserType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 */
#[\Symfony\Component\Routing\Attribute\Route('/user')]
class UserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface        $userService        user service
     * @param TranslatorInterface         $translator         translator
     * @param UserPasswordHasherInterface $userPasswordHasher password Hasher
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator, private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    /**
     * Action index.
     *
     * @param int $page Page
     *
     * @return Response Response
     */
    #[\Symfony\Component\Routing\Attribute\Route(name: 'user_index', methods: 'GET')]
    #[IsGranted('ROLE_USER')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->userService->getPaginatedList($page);

        return $this->render('user/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Action show.
     *
     * @param User $user User
     *
     * @return Response Response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}', name: 'user_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    #[IsGranted('ROLE_ADMIN')]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Action Create.
     *
     * @param Request $request Request
     *
     * @return Response Response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/create', name: 'user_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/create.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Action Edit.
     *
     * @param Request $request Request
     * @param User    $user    User
     *
     * @return Response Response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/edit', name: 'user_edit', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'POST', 'PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user, [
            'method' => 'PUT',
            'action' => $this->generateUrl('user_edit', ['id' => $user->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPlainPassword()) {
                $user->setPassword(
                    $this->userPasswordHasher->hashPassword($user, $user->getPlainPassword())
                );
            }
            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Action Delete.
     *
     * @param Request $request Request
     * @param User    $user    User
     *
     * @return Response Response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/delete', name: 'user_delete', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, User $user): Response
    {
        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('user_delete', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->delete($user);

            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/delete.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Action Promote.
     *
     * @param Request $request Request
     * @param User    $user    user
     *
     * @return Response Response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/promote', name: 'user_promote', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function promoteUserToAdmin(Request $request, User $user): Response
    {
        $form = $this->createForm(PromoteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->promoteUserToAdmin($user);

            $this->addFlash('success', $this->translator->trans('message.user_promoted_to_admin'));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/promote.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Action Revoke.
     *
     * @param Request $request Request
     * @param User    $user    User
     *
     * @return Response Response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/revoke', name: 'user_revoke', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function revokeAdminPrivilegesFromUser(Request $request, User $user): Response
    {
        if ($this->userService->isLastAdmin($user)) {
            $this->addFlash('error', $this->translator->trans('message.no_access'));

            return $this->redirectToRoute('user_index');
        }

        $form = $this->createForm(RevokeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->revokeAdminPrivilegesFromUser($user);

            $this->addFlash('success', $this->translator->trans('message.admin_privileges_revoked_from_user'));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/revoke.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Action change nickname.
     *
     * @param Request $request Request
     *
     * @return Response response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/change_nickname', name: 'change_nickname', methods: ['GET', 'POST'])]
    public function changeNickname(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangeNicknameType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->changeNickname($user, $form->get('nickname')->getData());

            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render('user/change_nickname.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Method block.
     *
     * @param User $user User
     *
     * @return Response Response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/block', name: 'user_block', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function blockUser(User $user): Response
    {
        $this->userService->blockUser($user);
        $this->addFlash('success', $this->translator->trans('message.user_blocked'));

        return $this->redirectToRoute('user_index');
    }

    /**
     * Action Unblock.
     *
     * @param User $user User
     *
     * @return Response Response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/unblock', name: 'user_unblock', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function unblockUser(User $user): Response
    {
        $this->userService->unblockUser($user);
        $this->addFlash('success', $this->translator->trans('message.user_unblocked'));

        return $this->redirectToRoute('user_index');
    }
}
