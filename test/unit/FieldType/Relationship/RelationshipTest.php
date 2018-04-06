<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship;

use PHPUnit\Framework\TestCase;
use Mockery as M;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\SectionConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\Relationship\Relationship
 * @covers ::<private>
 */
class RelationshipTest extends TestCase
{
    use M\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @var FormBuilderInterface|M\Mock */
    private $formBuilder;

    /** @var SectionInterface|M\Mock */
    private $section;

    /** @var CommonSectionInterface|M\Mock */
    private $sectionEntity;

    /** @var SectionManagerInterface|M\Mock */
    private $sectionManager;

    /** @var ReadSectionInterface|M\Mock */
    private $readSection;

    public function setUp()
    {
        $this->formBuilder = M::mock(FormBuilderInterface::class);
        $this->section = M::mock(SectionInterface::class);
        $this->sectionEntity = M::mock(CommonSectionInterface::class);
        $this->sectionManager = M::mock(SectionManagerInterface::class);
        $this->readSection = M::mock(ReadSectionInterface::class);
    }

    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form_one_to_many()
    {
        $relation = new Relationship();
        $fieldConfig = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'kind' => 'one-to-many',
                        'to' => 'pluto',
                        'form' => ['all' => ['relations' => 'something']]
                    ]
            ]
        );
        $relation->setConfig($fieldConfig);

        $sectionTo = M::mock(SectionInterface::class)->makePartial();

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andReturn($sectionTo);

        $sectionConfigTo = SectionConfig::fromArray(
            [
                'section' => [
                    'name' => 'nameOfSection',
                    'handle' => 'handleOfSection',
                    'fields' => ['1', '2', '3'],
                    'default' => 'sexyPerDefault',
                    'namespace' => 'the space has no name'
                ]
            ]
        );

        $sectionTo->shouldReceive('getConfig')
            ->once()
            ->andReturn($sectionConfigTo);

        $sectionEntities = M::mock('alias:sexyEntities')->makePartial();

        $this->sectionEntity->shouldReceive('getId')
            ->twice()
            ->andReturn(1);

        $this->sectionEntity->shouldReceive('getPlutos')
            ->once()
            ->andReturn($sectionEntities);

        $mockEntry = M::mock('alias:entry')->makePartial();
        $mockEntry->shouldReceive('getDefault')
            ->once()
            ->andReturn('planetarySexyEntry');

        $mockEntry->shouldReceive('getSlug')
            ->once()
            ->andReturn('planetary-sexy-entry');

        $this->readSection->shouldReceive('read')
            ->once()
            ->andReturn(new \ArrayIterator([$mockEntry]));

        $sectionEntities->shouldReceive('toArray')
            ->once()
            ->andReturn(['Uranus, Mars, Venus']);

        $this->formBuilder->shouldReceive('add')
            ->once()
            ->with(
                'plutos',
                ChoiceType::class,
                [
                    'choices' => ['planetarySexyEntry' => 'planetary-sexy-entry'],
                    'data' => ['Uranus, Mars, Venus'],
                    'multiple' => true,
                    'relations' => 'something'
                ]
            )
            ->andReturn($this->formBuilder);

        $this->formBuilder->shouldReceive('addModelTransformer')
            ->once();

        $this->formBuilder->shouldReceive('get')
            ->once()
            ->andReturn($this->formBuilder);


        $relation->addToForm(
            $this->formBuilder,
            $this->section,
            $this->sectionEntity,
            $this->sectionManager,
            $this->readSection
        );

        $this->assertInstanceOf(Relationship::class, $relation);
        $this->assertEquals($relation->getConfig(), $fieldConfig);
    }


    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form_one_to_many_when_no_children_are_found()
    {
        $relation = new Relationship();
        $fieldConfig = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'kind' => 'one-to-many',
                        'to' => 'pluto',
                        'form' => ['all' => ['relations' => 'something']]
                    ]
            ]
        );
        $relation->setConfig($fieldConfig);

        $sectionTo = M::mock(SectionInterface::class)->makePartial();

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andReturn($sectionTo);

        $sectionConfigTo = SectionConfig::fromArray(
            [
                'section' => [
                    'name' => 'nameOfSection',
                    'handle' => 'handleOfSection',
                    'fields' => ['1', '2', '3'],
                    'default' => 'sexyPerDefault',
                    'namespace' => 'the space has no name'
                ]
            ]
        );

        $sectionTo->shouldReceive('getConfig')
            ->once()
            ->andReturn($sectionConfigTo);

        $sectionEntities = M::mock('alias:sexyEntities')->makePartial();

        $this->sectionEntity->shouldReceive('getId')
            ->twice()
            ->andReturn(1);

        $this->sectionEntity->shouldReceive('getPlutos')
            ->once()
            ->andReturn(null);

        $mockEntry = M::mock('alias:entry')->makePartial();
        $mockEntry->shouldReceive('getDefault')
            ->once()
            ->andReturn('planetarySexyEntry');

        $mockEntry->shouldReceive('getSlug')
            ->once()
            ->andReturn('planetary-sexy-entry');

        $this->readSection->shouldReceive('read')
            ->once()
            ->andReturn(new \ArrayIterator([$mockEntry]));

        $sectionEntities->shouldReceive('toArray')
            ->never();

        $this->formBuilder->shouldReceive('add')
            ->once()
            ->with(
                'plutos',
                ChoiceType::class,
                [
                    'choices' => ['planetarySexyEntry' => 'planetary-sexy-entry'],
                    'data' => null,
                    'multiple' => true,
                    'relations' => 'something'
                ]
            )
            ->andReturn($this->formBuilder);

        $this->formBuilder->shouldReceive('addModelTransformer')
            ->once();

        $this->formBuilder->shouldReceive('get')
            ->once()
            ->andReturn($this->formBuilder);

        $relation->addToForm(
            $this->formBuilder,
            $this->section,
            $this->sectionEntity,
            $this->sectionManager,
            $this->readSection
        );

        $this->assertInstanceOf(Relationship::class, $relation);
        $this->assertEquals($relation->getConfig(), $fieldConfig);
    }

    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form_many_to_one()
    {
        $relation = new Relationship();
        $fieldConfig = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'kind' => 'many-to-one',
                        'to' => 'neptune',
                        'form' => ['all' => ['relations' => 'something']],
                        'variant' => 'not the variant you are looking for'
                    ]
            ]
        );
        $relation->setConfig($fieldConfig);

        $sectionTo = M::mock(SectionInterface::class)->makePartial();

        $this->sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(1);

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andReturn($sectionTo);

        $sectionConfigTo = SectionConfig::fromArray(
            [
                'section' => [
                    'name' => 'nameOfSection',
                    'handle' => 'handleOfSection',
                    'fields' => ['1', '2', '3'],
                    'default' => 'sexyPerDefault',
                    'namespace' => 'the space has no name'
                ]
            ]
        );

        $sectionTo->shouldReceive('getConfig')
            ->once()
            ->andReturn($sectionConfigTo);

        $selectedEntity = M::mock('alias:selectedEntity')->makePartial();

        $this->sectionEntity->shouldReceive('getNeptune')
            ->once()
            ->andReturn($selectedEntity);

        $mockEntry = M::mock('alias:entry')->makePartial();
        $mockEntry->shouldReceive('getDefault')
            ->once()
            ->andReturn('planetarySexyEntry');

        $mockEntry->shouldReceive('getSlug')
            ->once()
            ->andReturn('planetary-sexy-entry');

        $this->readSection->shouldReceive('read')
            ->once()
            ->andReturn(new \ArrayIterator([$mockEntry]));

        $this->formBuilder->shouldReceive('add')
            ->once()
            ->andReturn($this->formBuilder);

        $this->formBuilder->shouldReceive('addModelTransformer')
            ->once();

        $this->formBuilder->shouldReceive('get')
            ->once()
            ->andReturn($this->formBuilder);

        $relation->addToForm(
            $this->formBuilder,
            $this->section,
            $this->sectionEntity,
            $this->sectionManager,
            $this->readSection
        );

        $this->assertInstanceOf(Relationship::class, $relation);
        $this->assertEquals($relation->getConfig(), $fieldConfig);
    }

    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form_many_to_one_with_field_alias()
    {
        $relation = new Relationship();
        $fieldConfig = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'kind' => 'many-to-one',
                        'to' => 'neptune',
                        'as' => 'somethingElse',
                        'form' => ['all' => ['relations' => 'something']],
                        'variant' => 'not the variant you are looking for'
                    ]
            ]
        );
        $relation->setConfig($fieldConfig);

        $sectionTo = M::mock(SectionInterface::class)->makePartial();

        $this->sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(1);

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andReturn($sectionTo);

        $sectionConfigTo = SectionConfig::fromArray(
            [
                'section' => [
                    'name' => 'nameOfSection',
                    'handle' => 'handleOfSection',
                    'fields' => ['1', '2', '3'],
                    'default' => 'sexyPerDefault',
                    'namespace' => 'the space has no name'
                ]
            ]
        );

        $sectionTo->shouldReceive('getConfig')
            ->once()
            ->andReturn($sectionConfigTo);

        $selectedEntity = M::mock('alias:selectedEntity')->makePartial();

        $this->sectionEntity->shouldReceive('getSomethingElse')
            ->once()
            ->andReturn($selectedEntity);

        $mockEntry = M::mock('alias:entry')->makePartial();
        $mockEntry->shouldReceive('getDefault')
            ->once()
            ->andReturn('planetarySexyEntry');

        $mockEntry->shouldReceive('getSlug')
            ->once()
            ->andReturn('planetary-sexy-entry');

        $this->readSection->shouldReceive('read')
            ->once()
            ->andReturn(new \ArrayIterator([$mockEntry]));

        $this->formBuilder->shouldReceive('add')
            ->once()
            ->andReturn($this->formBuilder);

        $this->formBuilder->shouldReceive('addModelTransformer')
            ->once();

        $this->formBuilder->shouldReceive('get')
            ->once()
            ->andReturn($this->formBuilder);

        $relation->addToForm(
            $this->formBuilder,
            $this->section,
            $this->sectionEntity,
            $this->sectionManager,
            $this->readSection
        );

        $this->assertInstanceOf(Relationship::class, $relation);
        $this->assertEquals($relation->getConfig(), $fieldConfig);
    }

    /**
     * @todo: Fixe test
     */
    public function it_adds_to_form_many_to_many()
    {
        $relation = new Relationship();
        $fieldConfig = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'kind' => 'many-to-many',
                        'to' => 'mistletoeRedpole',
                        'form' => ['all' => ['mapped' => false]],
                        'variant' => 'not the variant you are looking for'
                    ]
            ]
        );
        $relation->setConfig($fieldConfig);

        $sectionTo = M::mock(SectionInterface::class)->makePartial();

        $this->sectionEntity->shouldReceive('getId')
            ->twice()
            ->andReturn(1);

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andReturn($sectionTo);

        $sectionConfigTo = SectionConfig::fromArray(
            [
                'section' => [
                    'name' => 'nameOfSection',
                    'handle' => 'handleOfSection',
                    'fields' => ['1', '2', '3'],
                    'default' => 'sexyPerDefault',
                    'namespace' => 'the space has no name'
                ]
            ]
        );

        $sectionTo->shouldReceive('getConfig')
            ->once()
            ->andReturn($sectionConfigTo);

        $selectedEntity = M::mock('alias:selectedEntity')->makePartial();

        $this->sectionEntity->shouldReceive('getMistletoeRedpoles')
            ->once()
            ->andReturn($selectedEntity);

        $selectedEntity->shouldReceive('toArray')
            ->once()
            ->andReturn(['MarinatedHotham', 'GravyCreamBlizzard']);

        $mockEntry = M::mock('alias:entry')->makePartial();
        $mockEntry->shouldReceive('getDefault')
            ->once()
            ->andReturn('Red-Cloaked BrightBalls');

        $mockEntry->shouldReceive('getSlug')
            ->once()
            ->andReturn('planetary-sexy-entry');

        $this->readSection->shouldReceive('read')
            ->once()
            ->andReturn(new \ArrayIterator([$mockEntry]));

        $this->formBuilder->shouldReceive('add')
            ->once()
            ->andReturn($this->formBuilder);

        $this->formBuilder->shouldReceive('addModelTransformer')
            ->once();

        $this->formBuilder->shouldReceive('get')
            ->once()
            ->andReturn($this->formBuilder);

        $relation->addToForm(
            $this->formBuilder,
            $this->section,
            $this->sectionEntity,
            $this->sectionManager,
            $this->readSection
        );

        $this->assertInstanceOf(Relationship::class, $relation);
        $this->assertEquals($relation->getConfig(), $fieldConfig);
    }


    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form_many_to_many_when_no_relations_are_found()
    {
        $relation = new Relationship();
        $fieldConfig = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'kind' => 'many-to-many',
                        'to' => 'mistletoeRedpole',
                        'form' => ['all' => ['relations' => 'something']],
                        'variant' => 'not the variant you are looking for'
                    ]
            ]
        );
        $relation->setConfig($fieldConfig);

        $sectionTo = M::mock(SectionInterface::class)->makePartial();

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andReturn($sectionTo);

        $this->sectionEntity->shouldReceive('getId')
            ->twice()
            ->andReturn(1);

        $sectionConfigTo = SectionConfig::fromArray(
            [
                'section' => [
                    'name' => 'nameOfSection',
                    'handle' => 'handleOfSection',
                    'fields' => ['1', '2', '3'],
                    'default' => 'sexyPerDefault',
                    'namespace' => 'the space has no name'
                ]
            ]
        );

        $sectionTo->shouldReceive('getConfig')
            ->once()
            ->andReturn($sectionConfigTo);

        $selectedEntity = M::mock('alias:selectedEntity')->makePartial();

        $this->sectionEntity->shouldReceive('getMistletoeRedpoles')
            ->once()
            ->andReturn(null);

        $selectedEntity->shouldReceive('toArray')
            ->never();

        $mockEntry = M::mock('alias:entry')->makePartial();
        $mockEntry->shouldReceive('getDefault')
            ->once()
            ->andReturn('Red-Cloaked BrightBalls');

        $mockEntry->shouldReceive('getSlug')
            ->once()
            ->andReturn('red-cloaked-brightballs');

        $this->readSection->shouldReceive('read')
            ->once()
            ->andReturn(new \ArrayIterator([$mockEntry]));

        $this->formBuilder->shouldReceive('add')
            ->once()
            ->with(
                'mistletoeRedpoles',
                ChoiceType::class,
                [
                    'choices' => ['Red-Cloaked BrightBalls' => 'red-cloaked-brightballs'],
                    'data' => null,
                    'multiple' => true,
                    'relations' => 'something'
                ]
            )
            ->andReturn($this->formBuilder);

        $this->formBuilder->shouldReceive('addModelTransformer')
            ->once();

        $this->formBuilder->shouldReceive('get')
            ->once()
            ->andReturn($this->formBuilder);

        $relation->addToForm(
            $this->formBuilder,
            $this->section,
            $this->sectionEntity,
            $this->sectionManager,
            $this->readSection
        );

        $this->assertInstanceOf(Relationship::class, $relation);
        $this->assertEquals($relation->getConfig(), $fieldConfig);
    }

    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form_one_to_one()
    {
        $relation = new Relationship();
        $fieldConfig = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'kind' => 'one-to-one',
                        'to' => 'neptune',
                        'form' => ['all' => ['relations' => 'something']],
                        'variant' => 'not the variant you are looking for'
                    ]
            ]
        );
        $relation->setConfig($fieldConfig);

        $sectionTo = M::mock(SectionInterface::class)->makePartial();

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andReturn($sectionTo);

        $sectionConfigTo = SectionConfig::fromArray(
            [
                'section' => [
                    'name' => 'nameOfSection',
                    'handle' => 'handleOfSection',
                    'fields' => ['1', '2', '3'],
                    'default' => 'sexyPerDefault',
                    'namespace' => 'the space has no name'
                ]
            ]
        );

        $sectionTo->shouldReceive('getConfig')
            ->once()
            ->andReturn($sectionConfigTo);

        $selectedEntity = M::mock('alias:selectedEntity')->makePartial();

        $this->sectionEntity->shouldReceive('getId')
            ->twice()
            ->andReturn(1);

        $this->sectionEntity->shouldReceive('getNeptune')
            ->once()
            ->andReturn($selectedEntity);

        $mockEntry = M::mock('alias:entry')->makePartial();
        $mockEntry->shouldReceive('getDefault')
            ->once()
            ->andReturn('planetarySexyEntry');

        $mockEntry->shouldReceive('getSlug')
            ->once()
            ->andReturn('planetary-sexy-entry');

        $this->readSection->shouldReceive('read')
            ->once()
            ->andReturn(new \ArrayIterator([$mockEntry]));

        $this->formBuilder->shouldReceive('add')
            ->once()
            ->andReturn($this->formBuilder);

        $this->formBuilder->shouldReceive('addModelTransformer')
            ->once();

        $this->formBuilder->shouldReceive('get')
            ->once()
            ->andReturn($this->formBuilder);

        $relation->addToForm(
            $this->formBuilder,
            $this->section,
            $this->sectionEntity,
            $this->sectionManager,
            $this->readSection
        );

        $this->assertInstanceOf(Relationship::class, $relation);
        $this->assertEquals($relation->getConfig(), $fieldConfig);
    }

    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form_one_to_one_with_field_alias()
    {
        $relation = new Relationship();
        $fieldConfig = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'kind' => 'one-to-one',
                        'to' => 'neptune',
                        'as' => 'somethingElse',
                        'form' => ['all' => ['relations' => 'something']],
                        'variant' => 'not the variant you are looking for'
                    ]
            ]
        );
        $relation->setConfig($fieldConfig);

        $sectionTo = M::mock(SectionInterface::class)->makePartial();

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andReturn($sectionTo);

        $sectionConfigTo = SectionConfig::fromArray(
            [
                'section' => [
                    'name' => 'nameOfSection',
                    'handle' => 'handleOfSection',
                    'fields' => ['1', '2', '3'],
                    'default' => 'sexyPerDefault',
                    'namespace' => 'the space has no name'
                ]
            ]
        );

        $sectionTo->shouldReceive('getConfig')
            ->once()
            ->andReturn($sectionConfigTo);

        $selectedEntity = M::mock('alias:selectedEntity')->makePartial();

        $this->sectionEntity->shouldReceive('getId')
            ->twice()
            ->andReturn(1);

        $this->sectionEntity->shouldReceive('getSomethingElse')
            ->once()
            ->andReturn($selectedEntity);

        $mockEntry = M::mock('alias:entry')->makePartial();
        $mockEntry->shouldReceive('getDefault')
            ->once()
            ->andReturn('planetarySexyEntry');

        $mockEntry->shouldReceive('getSlug')
            ->once()
            ->andReturn('planetary-sexy-entry');

        $this->readSection->shouldReceive('read')
            ->once()
            ->andReturn(new \ArrayIterator([$mockEntry]));

        $this->formBuilder->shouldReceive('add')
            ->once()
            ->andReturn($this->formBuilder);

        $this->formBuilder->shouldReceive('addModelTransformer')
            ->once();

        $this->formBuilder->shouldReceive('get')
            ->once()
            ->andReturn($this->formBuilder);

        $relation->addToForm(
            $this->formBuilder,
            $this->section,
            $this->sectionEntity,
            $this->sectionManager,
            $this->readSection
        );

        $this->assertInstanceOf(Relationship::class, $relation);
        $this->assertEquals($relation->getConfig(), $fieldConfig);
    }
}
