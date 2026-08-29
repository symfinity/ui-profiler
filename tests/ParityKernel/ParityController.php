<?php

declare(strict_types=1);

namespace Symfinity\UiProfiler\Tests\ParityKernel;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfinity\UiProfiler\Tests\ParityKernel\Form\ParityTextType;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

final class ParityController
{
    public function home(): Response
    {
        return new Response('<html><body><h1>parity home</h1></body></html>');
    }

    public function hostileCss(): Response
    {
        return new Response(<<<'HTML'
            <html>
            <head>
                <style>body a { color: hotpink !important; }</style>
            </head>
            <body><a href="/">hostile link</a></body>
            </html>
            HTML);
    }

    public function exception(): Response
    {
        throw new HttpException(500, 'parity fixture exception');
    }

    public function ajax(): Response
    {
        return new Response('{"status":"ok"}', 200, ['Content-Type' => 'application/json']);
    }

    public function rich(
        #[Autowire(service: 'doctrine.dbal.default_connection')]
        Connection $connection,
        TranslatorInterface $translator,
        Environment $twig,
        MailerInterface $mailer,
        MessageBusInterface $messageBus,
        HttpClientInterface $httpClient,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        #[Autowire(service: 'parity.cache')]
        CacheInterface $parityCache,
        ValidatorInterface $validator,
        FormFactoryInterface $formFactory,
    ): Response {
        $connection->executeQuery('SELECT 1')->fetchOne();
        $translator->trans('parity.fixture.message');
        $twig->render('@parity/parity_probe.html.twig', ['label' => 'parity twig probe']);
        $mailer->send(
            (new Email())
                ->from('parity@example.test')
                ->to('recipient@example.test')
                ->text('parity mailer fixture'),
        );
        $messageBus->dispatch(new ParityMessage());
        $httpClient->request('GET', 'https://parity.example.test/probe');
        $serializer->serialize(['parity' => 'fixture'], 'json');
        $logger->info('parity fixture log message');
        $parityCache->get('parity_key', static fn (): string => 'parity-cache-value');
        $validator->validate('', [new NotBlank()]);
        $formFactory->create(ParityTextType::class);

        return new Response('<html><body><h1>parity rich</h1></body></html>');
    }

    public function formError(Request $request, FormFactoryInterface $formFactory): Response
    {
        $form = $formFactory->create(ParityTextType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $form->submit(['text' => '']);
        }

        return new Response('<html><body><h1>parity form error</h1></body></html>');
    }
}
