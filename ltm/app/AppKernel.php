<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Dependencies of the CmfBlockBundle

            // Dependencies of the CmfMenuBundle
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            //new Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            //new Symfony\Cmf\Bundle\ContentBundle\CmfContentBundle(),
            new LTM\PortailBundle\PortailBundle(),
            new \FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new ADesigns\CalendarBundle\ADesignsCalendarBundle(),
            new Nzo\UrlEncryptorBundle\NzoUrlEncryptorBundle(),
            new Payum\Bundle\PayumBundle\PayumBundle(),
            new Oneup\UploaderBundle\OneupUploaderBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new Craue\FormFlowBundle\CraueFormFlowBundle(),
            new Spy\TimelineBundle\SpyTimelineBundle(),
            new Ornicar\AkismetBundle\OrnicarAkismetBundle(),
            new FOS\MessageBundle\FOSMessageBundle(),
            new Knp\Bundle\TimeBundle\KnpTimeBundle(),

            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\AopBundle\JMSAopBundle(),

            new JMS\TranslationBundle\JMSTranslationBundle(),
            new JMS\I18nRoutingBundle\JMSI18nRoutingBundle(),



            new FOS\RestBundle\FOSRestBundle(),
            new FOS\CommentBundle\FOSCommentBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this)

        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Acme\DemoBundle\AcmeDemoBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
