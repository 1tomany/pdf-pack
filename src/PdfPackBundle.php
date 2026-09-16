<?php

namespace OneToMany\PdfPack;

use OneToMany\PdfPack\Bridge\Mock\MockProvider;
use OneToMany\PdfPack\Bridge\Poppler\PopplerProvider;
use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Contract\PdfClientInterface;
use OneToMany\PdfPack\Resource\Registry;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function array_column;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

final class PdfPackBundle extends AbstractBundle
{
    private const string PROVIDER_TAG = 'onetomany.pdfpack.provider';

    protected string $extensionAlias = 'onetomany_pdfpack';

    /**
     * @see Symfony\Component\Config\Definition\ConfigurableInterface
     *
     * @param DefinitionConfigurator<'array'> $definition
     */
    #[\Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition
            ->rootNode()
                ->children()
                    ->enumNode('vendor')
                        ->values(array_column(Vendor::cases(), 'value'))
                        ->defaultValue(Vendor::Poppler->value)
                    ->end()
                    ->arrayNode('poppler_provider')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->stringNode('pdfinfo_binary')
                                ->cannotBeEmpty()
                                ->defaultValue('pdfinfo')
                            ->end()
                            ->stringNode('pdftoppm_binary')
                                ->cannotBeEmpty()
                                ->defaultValue('pdftoppm')
                            ->end()
                            ->stringNode('pdftotext_binary')
                                ->cannotBeEmpty()
                                ->defaultValue('pdftotext')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ConfigurableExtensionInterface
     *
     * @param array{
     *   vendor: non-empty-string,
     *   poppler_provider: array{
     *     pdfinfo_binary: non-empty-string,
     *     pdftoppm_binary: non-empty-string,
     *     pdftotext_binary: non-empty-string,
     *   },
     * } $config
     */
    #[\Override]
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $builder
            ->registerForAutoconfiguration(ProviderInterface::class)
            ->addTag(self::PROVIDER_TAG);

        $container
            ->services()
                ->set(MockProvider::class)
                    ->tag(self::PROVIDER_TAG)

                ->set(PopplerProvider::class)
                    ->arg('$pdfInfoBinary', $config['poppler_provider']['pdfinfo_binary'])
                    ->arg('$pdfToPpmBinary', $config['poppler_provider']['pdftoppm_binary'])
                    ->arg('$pdfToTextBinary', $config['poppler_provider']['pdftotext_binary'])
                    ->tag(self::PROVIDER_TAG)

                ->set(Registry::class)
                    ->arg('$providers', tagged_iterator(self::PROVIDER_TAG))

                ->set(PdfClient::class)
                    ->arg('$defaultVendor', $config['vendor'])
                    ->arg('$providers', service(Registry::class))
                    ->alias(PdfClientInterface::class, service(PdfClient::class))
        ;
    }
}
