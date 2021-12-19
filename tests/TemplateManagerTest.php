<?php

require_once __DIR__ . '/../src/Entity/Destination.php';
require_once __DIR__ . '/../src/Entity/Quote.php';
require_once __DIR__ . '/../src/Entity/Site.php';
require_once __DIR__ . '/../src/Entity/Template.php';
require_once __DIR__ . '/../src/Entity/User.php';
require_once __DIR__ . '/../src/Helper/SingletonTrait.php';
require_once __DIR__ . '/../src/Context/ApplicationContext.php';
require_once __DIR__ . '/../src/Repository/Repository.php';
require_once __DIR__ . '/../src/Repository/DestinationRepository.php';
require_once __DIR__ . '/../src/Repository/QuoteRepository.php';
require_once __DIR__ . '/../src/Repository/SiteRepository.php';
require_once __DIR__ . '/../src/TemplateManager.php';

class TemplateManagerTest extends PHPUnit_Framework_TestCase
{
	protected $templateManager;

    /**
     * Init the mocks
     */
    public function setUp()
    {
		$this->templateManager = new TemplateManager();
    }

    /**
     * Closes the mocks
     */
    public function tearDown()
    {
    }

    /**
     * @test
	 * @testdox overall test of TemplateManager
     */
    public function test()
    {
        $faker = \Faker\Factory::create();

        $expectedDestination = DestinationRepository::getInstance()->getById($faker->randomNumber());
        $expectedUser = ApplicationContext::getInstance()->getCurrentUser();

        $quote = new Quote($faker->randomNumber(), $faker->randomNumber(), $expectedDestination->id, $faker->date());

        $template = new Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name]',
            "
Bonjour [user:first_name],

Merci d'avoir contacté un agent local pour votre voyage [quote:destination_name].

Bien cordialement,

L'équipe Evaneos.com
www.evaneos.com
");

        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals('Votre voyage avec une agence locale ' . $expectedDestination->countryName, $message->subject);
        $this->assertEquals("
Bonjour " . $expectedUser->firstname . ",

Merci d'avoir contacté un agent local pour votre voyage " . $expectedDestination->countryName . ".

Bien cordialement,

L'équipe Evaneos.com
www.evaneos.com
", $message->content);
    }

	/**
	 * @test
	 * @testdox raise error is no template is given
	 */
	public function testException() {
		$this->expectException(TypeError::class);

        $message = $this->templateManager->getTemplateComputed(null, []);
	}

	/**
	 * @test
	 * @testdox use user from application context
	 */
	public function testUserFromContext() {
        $expectedUser = ApplicationContext::getInstance()->getCurrentUser();

		$template = new Template(1, '[user:first_name]', '');
        $message = $this->templateManager->getTemplateComputed($template, []);

        $this->assertEquals($expectedUser->firstname, $message->subject);
	}

	/**
	 * @test
	 * @testdox use user from input data
	 */
	public function testUserFromData() {
        $expectedUser = new User(1, 'Jean', 'Valjean', 'j.valjean@tenardier.inn');

		$template = new Template(1, '[user:first_name]', '');
        $message = $this->templateManager->getTemplateComputed($template, [ 'user' => $expectedUser ]);

        $this->assertEquals($expectedUser->firstname, $message->subject);
	}

	/**
	 * @test
	 * @testdox renders quote summary in HTML and plain text
	 */
	public function testQuoteSummary() {
        $faker = \Faker\Factory::create();
        $quote = new Quote($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), $faker->date());

		$template = new Template(1, '[quote:summary_html]', '[quote:summary]');
        $message = $this->templateManager->getTemplateComputed($template, [ 'quote' => $quote ]);

        $this->assertEquals('<p>' . $quote->id . '</p>', $message->subject);
        $this->assertEquals($quote->id, $message->content);
	}

	/**
	 * @test
	 * @testdox renders destination name
	 */
	public function testDestination() {
        $faker = \Faker\Factory::create();

        $expectedDestination = DestinationRepository::getInstance()->getById($faker->randomNumber());
        $quote = new Quote($faker->randomNumber(), $faker->randomNumber(), $expectedDestination->id, $faker->date());

		$template = new Template(1, '[quote:destination_name]', '');
        $message = $this->templateManager->getTemplateComputed($template, [ 'quote' => $quote ]);

        $this->assertEquals($expectedDestination->countryName, $message->subject);
	}
}
