<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Email;

use PHPUnit\Framework\TestCase;
use Mockery as M;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\Email\Email
 * @covers ::<private>
 */
class EmailTest extends TestCase
{
    use M\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form()
    {
        $formBuilder = M::mock(FormBuilderInterface::class);
        $section = M::mock(SectionInterface::class);
        $sectionEntity = M::mock(CommonSectionInterface::class);
        $sectionManager = M::mock(SectionManagerInterface::class);
        $readSection = M::mock(ReadSectionInterface::class);
        $request = M::mock(Request::class);

        $email = new Email();
        $config = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'form' => [
                            'all' => [
                                'emailTesting' => true,
                                'purify_html' => true,
                            ]
                        ]
                    ]
            ]
        );
        $email->setConfig($config);
        $sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(40000);

        $formBuilder->shouldReceive('add')
            ->once()
            ->with(
                (string)$email->getConfig()->getHandle(),
                EmailType::class, [
                    'emailTesting' => true,
                    'purify_html' => true,
                    'constraints' => [
                        new \Symfony\Component\Validator\Constraints\Email([
                            'message' => 'form_error_invalid_email'
                        ])
                    ]
                ])
            ->andReturn($formBuilder);
        $email->addToForm(
            $formBuilder, $section, $sectionEntity,
            $sectionManager, $readSection, $request
        );

        $this->assertInstanceOf(Email::class, $email);
        $this->assertEquals($email->getConfig(), $config);
    }
}
