<?php
namespace DrdPlus\Tests\Person\Background;

use DrdPlus\Codes\ProfessionCodes;
use DrdPlus\Codes\SkillCodes;
use DrdPlus\Person\Background\BackgroundSkillPoints;
use DrdPlus\Professions\Profession;
use DrdPlus\Tables\Tables;

class BackgroundSkillPointsTest extends AbstractTestOfHeritageDependent
{
    /**
     * @test
     * @dataProvider provideSkillType
     * @param string $skillType
     */
    public function I_can_get_skill_points($skillType)
    {
        $backgroundPoints = $this->createBackgroundPoints($pointsValue = 7);
        $heritage = $this->createHeritage($heritageValue = 456);

        $backgroundSkills = BackgroundSkillPoints::getIt($backgroundPoints, $heritage);
        $tables = $this->mockery(Tables::class)
            ->shouldReceive('getBackgroundSkillsTable')
            ->atLeast()->once()
            ->andReturn($backgroundSkillsTable = $this->mockery(\stdClass::class))
            ->getMock();
        $backgroundSkillsTable->shouldReceive('getSkillPoints')
            ->with($pointsValue, ProfessionCodes::FIGHTER, $skillType)
            ->atLeast()->once()
            ->andReturn($result = 'foo')
            ->getMock();
        /** @var Tables $tables */
        $this->assertSame(
            $result,
            $backgroundSkills->getSkillPoints(
                $this->createProfession(ProfessionCodes::FIGHTER), $skillType, $tables
            )
        );
        switch ($skillType) {
            case SkillCodes::PHYSICAL :
                $this->assertSame(
                    $result,
                    $backgroundSkills->getPhysicalSkillPoints(
                        $this->createProfession(ProfessionCodes::FIGHTER),
                        $tables
                    )
                );
                break;
            case SkillCodes::PSYCHICAL :
                $this->assertSame(
                    $result,
                    $backgroundSkills->getPsychicalSkillPoints(
                        $this->createProfession(ProfessionCodes::FIGHTER),
                        $tables
                    )
                );
                break;
            case SkillCodes::COMBINED :
                $this->assertSame(
                    $result,
                    $backgroundSkills->getCombinedSkillPoints(
                        $this->createProfession(ProfessionCodes::FIGHTER),
                        $tables
                    )
                );
                break;
        }
    }

    /**
     * @param string $professionCode
     * @return \Mockery\MockInterface|Profession
     */
    private function createProfession($professionCode)
    {
        $profession = $this->mockery(Profession::class);
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);

        return $profession;
    }

    public function provideSkillType()
    {
        return [
            [SkillCodes::PHYSICAL],
            [SkillCodes::PSYCHICAL],
            [SkillCodes::COMBINED],
        ];
    }
}
