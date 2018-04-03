<?php

use App\Entity\Contact;
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use \Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context
{
    use KernelDictionary;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {

    }

    /**
     * @Given /^I start the scenario$/
     */
    public function iStartTheScenario()
    {

    }

    /**
     * @Then /^I recreate database$/
     */
    public function iRecreateDatabase1()
    {
        $application = new Application($this->getKernel());

        $application->setAutoExit(false);

        $application->run(new StringInput('doctrine:database:drop --force --env=dev'));

        $application->run(new StringInput('doctrine:database:create --env=dev'));

        $application->run(new StringInput('doctrine:migrations:migrate --no-interaction --env=dev'));
    }

    /**
     * @When I wait :duration sec
     */
    public function iWaitSec($duration)
    {
        $this->getSession()->wait($duration * 1000);
    }

    /**
     * @Given /^Contact "([^"]*)" should be in database$/
     */
    public function contactShouldBeInDatabase($email)
    {
        //$contact = $this->getContainer()->get('doctrine')->getRepository(Contact::class)->findBy(['email' => $email ]);
        $contact = $this->getContainer()->get('doctrine')->getRepository(Contact::class)->findOneByEmail($email);

        $nbContact = $this->getContainer()->get('doctrine')->getRepository(Contact::class)->countByEmail($email);

        if(!$contact) {
            throw new Exception('Email '.$email.' not found in database');
        }

        if($nbContact!==1) {
            throw new Exception($nbContact .'Email '.$email.' found in database');
        }

    }


}
