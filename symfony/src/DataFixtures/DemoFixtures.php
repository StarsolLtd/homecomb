<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Comment\ReviewComment;
use App\Entity\Image;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\ReviewSolicitation;
use App\Entity\Survey\Question;
use App\Entity\Survey\Survey;
use App\Entity\User;
use App\Service\ReviewService;
use App\Util\AgencyHelper;
use App\Util\BranchHelper;
use function copy;
use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use function preg_replace;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DemoFixtures extends AbstractDataFixtures implements DependentFixtureInterface
{
    private AgencyHelper $agencyHelper;
    private BranchHelper $branchHelper;
    private ReviewService $reviewService;
    private UserPasswordEncoderInterface $userPasswordEncoder;

    private const USER_1 = 'jack@mimas.io';
    private const USER_2 = 'andrea@starsol.co.uk';
    private const USER_3 = 'lauren@starsol.co.uk';
    private const USER_4 = 'zora@starsol.co.uk';
    private const USER_5 = 'jo@cambridgeresidential.com';

    public function __construct(
        AgencyHelper $agencyHelper,
        BranchHelper $branchHelper,
        ReviewService $reviewService,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->agencyHelper = $agencyHelper;
        $this->branchHelper = $branchHelper;
        $this->reviewService = $reviewService;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    protected function getEnvironments(): array
    {
        return ['dev'];
    }

    protected function doLoad(ObjectManager $manager): void
    {
        $users = $this->loadUsers($manager);

        $agencies = $this->loadAgencies($manager);
        $branches = $this->loadBranches($manager, $agencies);
        $this->loadReviewSolicitations($manager);

        $reviews = [];

        /** @var Property $property249 */
        $property249 = $this->getReference('property-'.PropertyFixtures::PROPERTY_249_VENDOR_PROPERTY_ID);
        /** @var Property $property25 */
        $property25 = $this->getReference('property-'.PropertyFixtures::PROPERTY_25_VENDOR_PROPERTY_ID);
        /** @var Property $property44 */
        $property44 = $this->getReference('property-'.PropertyFixtures::PROPERTY_44_VENDOR_PROPERTY_ID);

        $comment = (new ReviewComment())
            ->setPublished(true)
            ->setUser($users[self::USER_5])
            ->setContent(
                "Hello Jack! Thank you for the positive review. I'm sorry the decor was a bit dated "
                .'when you were living in number 249. The landlord has since renovated the property, and future '
                .'tenant will benefit from a more modern style of interior design.'
            )
        ;
        $manager->persist($comment);

        // TODO why would phpstan otherwise think this is a Comment and not a ReviewComment?
        /** @var ReviewComment $reviewComment */
        $reviewComment = $comment;

        $reviews[] = (new Review())
            ->setUser($users[self::USER_1])
            ->setProperty($property249)
            ->setBranch($branches[0])
            ->setTitle('Pleasant two year stay in great location')
            ->setAuthor('Jack Parnell')
            ->setContent(
                'I rented this home for two years. It is in a great location close to the city centre and the '
                .'agents were always pleasant when I dealt with them. The property was furnished, and while the '
                .'kitchen fittings were pretty good, the rest of the decor I think had not changed since the 1970s.'
            )
            ->setOverallStars(4)
            ->setAgencyStars(5)
            ->setLandlordStars(null)
            ->setPropertyStars(3)
            ->setPublished(true)
            ->addComment($reviewComment);

        $reviews[] = (new Review())
            ->setUser($users[self::USER_2])
            ->setProperty($property249)
            ->setBranch($branches[0])
            ->setTitle('I loved this place!')
            ->setAuthor('Andrea Németh')
            ->setContent(
                'This was the first home I ever rented and had a great couple of years here. It is right in the '
                .'middle of Cambridge, plenty of amenities within walking distance. It has a nice garden at the back '
                .'and the landlord kindly allowed us to keep our pet dog here. It is a great home!'
            )
            ->setOverallStars(5)
            ->setAgencyStars(null)
            ->setLandlordStars(5)
            ->setPropertyStars(5)
            ->setPublished(true);

        $reviews[] = (new Review())
            ->setUser($users[self::USER_3])
            ->setProperty($property249)
            ->setBranch($branches[4])
            ->setTitle('Great three years here')
            ->setAuthor('Lauren Marie')
            ->setContent(
                'I rented this place from January 2017 to March 2020. The landlord redecorated it before I moved '
                .'in which gave it a really fresh feel. If work was not taking me away from Cambridge, I could have '
                .'happily stayed here for years.'
            )
            ->setOverallStars(5)
            ->setAgencyStars(4)
            ->setLandlordStars(5)
            ->setPropertyStars(5)
            ->setPublished(true);

        $reviews[] = (new Review())
            ->setUser($users[self::USER_3])
            ->setProperty($property25)
            ->setBranch($branches[3])
            ->setTitle('Small flat suitable for student')
            ->setAuthor('Lauren Martin')
            ->setContent(
                'I rented this place for a few months when I first moved to Cambridge. It was very small and '
                .' I felt a bit cramped here. The bedroom was barely wider than a double bed. I think it would be a '
                .' great place for a student who is not bringing many possessions to Cambridge. '
            )
            ->setOverallStars(3)
            ->setAgencyStars(4)
            ->setLandlordStars(null)
            ->setPropertyStars(3)
            ->setPublished(true);

        $reviews[] = (new Review())
            ->setUser($users[self::USER_4])
            ->setProperty($property44)
            ->setBranch($branches[4])
            ->setTitle('Nice location but landlord never dealt with problems')
            ->setAuthor('Zora Smith')
            ->setContent(
                'This house is in a nice part of Cambridge, but the landlord was difficult. The power shower  '
                .'stopped working after a month, and despite sending multiple emails and leaving voicemails over the '
                .'course of several months, the landlord never arranged for it to be repaired or replaced. '
            )
            ->setOverallStars(3)
            ->setAgencyStars(5)
            ->setLandlordStars(1)
            ->setPropertyStars(4)
            ->setPublished(true);

        foreach ($reviews as $review) {
            $this->reviewService->generateLocales($review);
            $manager->persist($review);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LocaleFixtures::class,
            PropertyFixtures::class,
        ];
    }

    /**
     * @return Agency[]
     */
    private function loadAgencies(ObjectManager $manager): array
    {
        $cambridgeResidentialLogo = (new Image())->setImage('cambridge-residential.png')->setType(Image::TYPE_LOGO);
        $this->copyDemoImageToPublic('cambridge-residential.png');

        $abbeyShelfordLogo = (new Image())->setImage('abbey-shelford.png')->setType(Image::TYPE_LOGO);
        $this->copyDemoImageToPublic('abbey-shelford.png');

        $norwichHomesLogo = (new Image())->setImage('norwich-homes.png')->setType(Image::TYPE_LOGO);
        $this->copyDemoImageToPublic('norwich-homes.png');

        $clerkenwellLettingsLogo = (new Image())->setImage('clerkenwell-lettings.png')->setType(Image::TYPE_LOGO);
        $this->copyDemoImageToPublic('clerkenwell-lettings.png');

        $birminghamRentalsLogo = (new Image())->setImage('birmingham-rentals.png')->setType(Image::TYPE_LOGO);
        $this->copyDemoImageToPublic('birmingham-rentals.png');

        /** @var User $cambridgeResidentialAdmin */
        $cambridgeResidentialAdmin = $this->getReference('user-'.self::USER_5);

        $agencies = [
            (new Agency())->setName('Cambridge Residential')->addImage($cambridgeResidentialLogo)->addAdminUser($cambridgeResidentialAdmin),
            (new Agency())->setName('Abbey & Shelford')->addImage($abbeyShelfordLogo),
            (new Agency())->setName('Norwich Homes')->addImage($norwichHomesLogo),
            (new Agency())->setName('Clerkenwell Lettings')->addImage($clerkenwellLettingsLogo),
            (new Agency())->setName('Birmingham Rentals')->addImage($birminghamRentalsLogo),
        ];

        /** @var Agency $agency */
        foreach ($agencies as $agency) {
            $agency->setCountryCode('UK')
                ->setPublished(true)
                ->setCreatedAt(new DateTime('2020-11-27 12:00:00'))
                ->setUpdatedAt(new DateTime('2020-11-27 12:00:00'));

            $this->agencyHelper->generateSlug($agency);
            $manager->persist($agency);
        }

        return $agencies;
    }

    /**
     * @param Agency[] $agencies
     *
     * @return Branch[]
     */
    private function loadBranches(ObjectManager $manager, array $agencies): array
    {
        $branches = [];

        list($cambridgeAgency, $abbeyAgency, $norwichAgency) = $agencies;

        $cambridgeBranchNames = ['Arbury', 'Chesterton', 'Cherry Hinton'];
        foreach ($cambridgeBranchNames as $i => $branchName) {
            $branches[] = (new Branch())
                ->setAgency($cambridgeAgency)
                ->setName($branchName)
                ->setPublished(true)
                ->setTelephone('01223 '.$i.'00 0'.$i.'0')
                ->setEmail($this->getDemoEmail($branchName, $cambridgeAgency));
        }

        $abbeyBranchNames = ['Chesterton', 'Great Shelford', 'Waterbeach', 'Willingham'];
        foreach ($abbeyBranchNames as $branchName) {
            $branches[] = (new Branch())
                ->setAgency($abbeyAgency)
                ->setName($branchName)
                ->setPublished(true)
                ->setTelephone('01423 '.$i.'00 0'.$i.'0')
                ->setEmail($this->getDemoEmail($branchName, $abbeyAgency));
        }

        $norwichBranchNames = ['Drayton', 'Golden Triangle'];
        foreach ($norwichBranchNames as $branchName) {
            $branches[] = (new Branch())
                ->setAgency($norwichAgency)
                ->setName($branchName)
                ->setPublished(true)
                ->setTelephone('01603 '.$i.'00 0'.$i.'0')
                ->setEmail($this->getDemoEmail($branchName, $norwichAgency));
        }

        foreach ($branches as $branch) {
            $this->branchHelper->generateSlug($branch);
            $manager->persist($branch);

            $agency = $branch->getAgency();
            if (null !== $agency) {
                $this->addReference('branch-'.$agency->getName().'-'.$branch->getName(), $branch);
            }
        }

        $this->loadSurveys($manager);

        $manager->flush();

        return $branches;
    }

    /**
     * @return User[]
     */
    private function loadUsers(ObjectManager $manager): array
    {
        $data = [
            ['email' => self::USER_1, 'password' => 'To_The_Moon_2020', 'firstName' => 'Jack', 'lastName' => 'Parnell'],
            ['email' => self::USER_2, 'password' => 'Fire_Dragon_2020', 'firstName' => 'Andrea', 'lastName' => 'Nemeth'],
            ['email' => self::USER_3, 'password' => 'Ride_A_Bicycle_2020', 'firstName' => 'Lauren', 'lastName' => 'Marina'],
            ['email' => self::USER_4, 'password' => 'South_Tyrol_2020', 'firstName' => 'Zora', 'lastName' => 'Arbone'],
            ['email' => self::USER_5, 'password' => 'Cambridge_Residential_2020', 'firstName' => 'Jo', 'lastName' => 'Camberley'],
        ];

        $users = [];
        foreach ($data as $row) {
            $user = (new User())
                ->setEmail($row['email'])
                ->setFirstName($row['firstName'])
                ->setLastName($row['lastName'])
            ;
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $row['password']));
            $manager->persist($user);
            $this->addReference('user-'.$user->getEmail(), $user);
            $users[$row['email']] = $user;
        }

        $manager->flush();

        return $users;
    }

    private function loadReviewSolicitations(ObjectManager $manager): void
    {
        /** @var User $cambridgeResidentialAdmin */
        $cambridgeResidentialAdmin = $this->getReference('user-'.self::USER_5);

        /** @var Property $property17 */
        $property17 = $this->getReference('property-'.PropertyFixtures::PROPERTY_17_VENDOR_PROPERTY_ID);

        /** @var Branch $arburyBranch */
        $arburyBranch = $this->getReference('branch-Cambridge Residential-Arbury');

        $rs = (new ReviewSolicitation())
            ->setBranch($arburyBranch)
            ->setSenderUser($cambridgeResidentialAdmin)
            ->setProperty($property17)
            ->setRecipientFirstName('Anna')
            ->setRecipientLastName('Testinova')
            ->setRecipientEmail('anna.testinova@starsol.co.uk')
            ->setCode('73d2d50d17e8c1bbb05b8fddb3918033f2daf589')
        ;

        $manager->persist($rs);
        $manager->flush();
    }

    private function loadSurveys(ObjectManager $manager): void
    {
        $question1 = (new Question())
            ->setType('free')
            ->setContent('How does a Snickers make you feel?')
            ->setHelp('Maybe less hungry.')
            ->setSortOrder(1)
        ;

        $question2 = (new Question())
            ->setType('free')
            ->setContent('Where do you normally buy chocolate bars?')
            ->setSortOrder(2)
        ;

        $survey = (new Survey())
            ->setSlug('survey')
            ->setTitle('Chocolate bars of the UK')
            ->setDescription('Your thoughts on the options')
            ->setPublished(true)
            ->addQuestion($question1)
            ->addQuestion($question2)
        ;

        $manager->persist($survey);
    }

    private function getDemoImageFixturesPath(): string
    {
        return '/var/www/symfony/assets/demo/images/';
    }

    private function getPublicImagesPath(): string
    {
        return '/var/www/symfony/public/images/images/';
    }

    private function copyDemoImageToPublic(string $filename): void
    {
        copy($this->getDemoImageFixturesPath().$filename, $this->getPublicImagesPath().$filename);
    }

    private function getDemoEmail(string $branchName, Agency $agency): string
    {
        return strtolower(
            preg_replace('/[^A-Za-z0-9]/', '', $branchName)
            .'@'
            .preg_replace('/[^A-Za-z0-9]/', '', $agency->getName() ?? '')
            .'.com'
        );
    }
}
