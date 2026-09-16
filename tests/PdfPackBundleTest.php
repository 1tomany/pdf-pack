<?php

namespace OneToMany\PdfPack\Tests;

use OneToMany\PdfPack\Bridge\Mock\MockProvider;
use OneToMany\PdfPack\Bridge\Poppler\PopplerProvider;
use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Contract\PdfClientInterface;
use OneToMany\PdfPack\PdfClient;
use OneToMany\PdfPack\PdfPackBundle;
use OneToMany\PdfPack\Resource\Registry;
use OneToMany\PdfPack\Tests\Fixture\Bridge\ImagickProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[Group('UnitTests')]
final class PdfPackBundleTest extends TestCase
{
    public function testGettingExtensionAlias(): void
    {
        $this->assertSame('onetomany_pdfpack', new PdfPackBundle()->getContainerExtension()?->getAlias());
    }

    public function testRegisteringFacadeAndProviders(): void
    {
        $container = $this->loadExtension();

        $this->assertTrue($container->hasDefinition(MockProvider::class));
        $this->assertTrue($container->hasDefinition(PopplerProvider::class));
        $this->assertTrue($container->hasDefinition(Registry::class));
        $this->assertTrue($container->hasDefinition(PdfClient::class));
        $this->assertTrue($container->hasAlias(PdfClientInterface::class));
        $this->assertSame(PdfClient::class, $container->getAlias(PdfClientInterface::class)->__toString());
        $this->assertSame('poppler', $container->getDefinition(PdfClient::class)->getArgument('$defaultProvider'));
        $this->assertTrue($container->getAutoconfiguredInstanceof()[ProviderInterface::class]->hasTag('onetomany.pdfpack.provider'));
    }

    public function testConfiguredProviderIsUsedByAutowiredClient(): void
    {
        $container = $this->loadExtension(['provider' => 'mock']);
        $container->getAlias(PdfClientInterface::class)->setPublic(true);
        $container->compile();

        $client = $container->get(PdfClientInterface::class);

        $this->assertInstanceOf(PdfClient::class, $client);
        $this->assertSame(__FILE__, $client->files->read(__FILE__)->path);
    }

    public function testConfiguredProviderCannotBeEmpty(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->loadExtension(['provider' => '']);
    }

    public function testConfiguredThirdPartyProviderIsAutowired(): void
    {
        $container = $this->loadExtension([
            'provider' => 'imagick',
        ]);

        $container
            ->register(ImagickProvider::class)
            ->setAutoconfigured(true);

        $container
            ->getAlias(PdfClientInterface::class)
            ->setPublic(true);

        $container->compile();

        $client = $container->get(PdfClientInterface::class);

        $this->assertInstanceOf(PdfClient::class, $client);
        $this->assertSame(1, $client->files->read(__FILE__)->pageCount);
    }

    public function testConfiguringPopplerBinaries(): void
    {
        $container = $this->loadExtension([
            'poppler_provider' => [
                'pdfinfo_binary' => '/bin/pdfinfo',
                'pdftoppm_binary' => '/bin/pdftoppm',
                'pdftotext_binary' => '/bin/pdftotext',
            ],
        ]);
        $provider = $container->getDefinition(PopplerProvider::class);

        $this->assertSame('/bin/pdfinfo', $provider->getArgument('$pdfInfoBinary'));
        $this->assertSame('/bin/pdftoppm', $provider->getArgument('$pdfToPpmBinary'));
        $this->assertSame('/bin/pdftotext', $provider->getArgument('$pdfToTextBinary'));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function loadExtension(array $config = []): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $extension = new PdfPackBundle()->getContainerExtension();

        $this->assertNotNull($extension);
        $extension->load([$config], $container);

        return $container;
    }
}
