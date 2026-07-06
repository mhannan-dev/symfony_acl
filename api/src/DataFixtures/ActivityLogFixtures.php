<?php

namespace App\DataFixtures;

use App\Entity\ActivityLog;
use App\Entity\ContentType;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ActivityLogFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [UserFixtures::class, ContentTypeFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference(UserFixtures::USER_ADMIN, User::class);
        $ct = $this->getReference(ContentTypeFixtures::CONTENT_TYPE_USER, ContentType::class);

        $logs = [
            ['actionFlag' => 1, 'objectRepr' => 'Admin', 'changeMessage' => 'Initial fixtures loaded'],
            ['actionFlag' => 1, 'objectRepr' => 'John', 'changeMessage' => 'Initial fixtures loaded'],
            ['actionFlag' => 1, 'objectRepr' => 'Jane', 'changeMessage' => 'Initial fixtures loaded'],
        ];

        foreach ($logs as $data) {
            $log = new ActivityLog();
            $log->setActionTime(new \DateTime());
            $log->setActionFlag($data['actionFlag']);
            $log->setObjectRepr($data['objectRepr']);
            $log->setChangeMessage($data['changeMessage']);
            $log->setUser($user);
            $log->setContentType($ct);
            $manager->persist($log);
        }

        $manager->flush();
    }
}
