<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\User;
use App\Util\AgencyHelper;
use App\Util\BranchHelper;
use App\Util\PropertyHelper;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DemoFixtures extends Fixture
{
    private AgencyHelper $agencyHelper;
    private BranchHelper $branchHelper;
    private PropertyHelper $propertyHelper;
    private UserPasswordEncoderInterface $userPasswordEncoder;

    private const USER_1 = 'jack@mimas.io';
    private const USER_2 = 'andrea@starsol.co.uk';
    private const USER_3 = 'lauren@starsol.co.uk';
    private const USER_4 = 'zora@starsol.co.uk';

    public function __construct(
        AgencyHelper $agencyHelper,
        BranchHelper $branchHelper,
        PropertyHelper $propertyHelper,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->agencyHelper = $agencyHelper;
        $this->branchHelper = $branchHelper;
        $this->propertyHelper = $propertyHelper;
        $this->propertyHelper = $propertyHelper;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $agencies = $this->loadAgencies($manager);
        $branches = $this->loadBranches($manager, $agencies);

        $properties = $this->loadProperties($manager);

        $users = $this->loadUsers($manager);

        $reviews = [];

        $reviews[] = (new Review())
            ->setUser($users[self::USER_1])
            ->setProperty($properties[0])
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
            ->setPublished(true);

        $reviews[] = (new Review())
            ->setUser($users[self::USER_2])
            ->setProperty($properties[0])
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
            ->setProperty($properties[0])
            ->setBranch($branches[4])
            ->setTitle('I loved this place!')
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
            ->setProperty($properties[1])
            ->setBranch($branches[3])
            ->setTitle('Small flat suitable for student')
            ->setAuthor('Lauren Martin')
            ->setContent(
                'I rented this place for a few months when I first moved to Cambridge. It was very small and '
                .' I felt a bit cramped here. The bedroom was barely wider than a double bed. I think it would be a '
                .' great place for a student who is not brining many possessions to Cambridge. '
            )
            ->setOverallStars(3)
            ->setAgencyStars(4)
            ->setLandlordStars(null)
            ->setPropertyStars(3)
            ->setPublished(true);

        $reviews[] = (new Review())
            ->setUser($users[self::USER_4])
            ->setProperty($properties[2])
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
            $manager->persist($review);
        }

        $manager->flush();
    }

    /**
     * @return Agency[]
     */
    private function loadAgencies(ObjectManager $manager): array
    {
        $agencies = [
            (new Agency())->setName('Cambridge Residential'),
            (new Agency())->setName('Abbey & Shelford'),
            (new Agency())->setName('Norwich Homes'),
            (new Agency())->setName('Clerkenwell Lettings'),
            (new Agency())->setName('Birmingham Rentals'),
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
        foreach ($cambridgeBranchNames as $branchName) {
            $branches[] = (new Branch())
                ->setAgency($cambridgeAgency)
                ->setName($branchName)
                ->setPublished(true);
        }

        $abbeyBranchNames = ['Chesterton', 'Great Shelford', 'Waterbeach', 'Willingham'];
        foreach ($abbeyBranchNames as $branchName) {
            $branches[] = (new Branch())
                ->setAgency($abbeyAgency)
                ->setName($branchName)
                ->setPublished(true);
        }

        $norwichBranchNames = ['Drayton', 'Golden Triangle'];
        foreach ($norwichBranchNames as $branchName) {
            $branches[] = (new Branch())
                ->setAgency($norwichAgency)
                ->setName($branchName)
                ->setPublished(true);
        }

        foreach ($branches as $branch) {
            $this->branchHelper->generateSlug($branch);
            $manager->persist($branch);
        }

        $manager->flush();

        return $branches;
    }

    /**
     * @return Property[]
     */
    private function loadProperties(ObjectManager $manager): array
    {
        $properties = [];

        $properties[] = (new Property())
            ->setVendorPropertyId('ZmM5Yzc5MzMyODAyZTc4IDE3MDQ0OTcyIDMzZjhlNDFkNGU1MzY0Mw==')
            ->setAddressLine1('249 Victoria Road')
            ->setCity('Cambridge')
            ->setPostcode('CB4 3LF');

        $properties[] = (new Property())
            ->setVendorPropertyId('MjgwNjUwNjUwY2M3M2ViIDE2NTQ2NTY0IDMzZjhlNDFkNGU1MzY0Mw==')
            ->setAddressLine1('25 Bateman Street')
            ->setCity('Cambridge')
            ->setPostcode('CB2 1NB');

        $properties[] = (new Property())
            ->setVendorPropertyId('OWM3ODAxYzFiYTEzMzE3IDE2MzkxMzg2IDMzZjhlNDFkNGU1MzY0Mw==')
            ->setAddressLine1('44 Fanshawe Road')
            ->setCity('Cambridge')
            ->setPostcode('CB1 3QY');

        $properties[] = (new Property())
            ->setVendorPropertyId('NGViYmZiZjY5YjBiYTAyIDE2NjYxMjMzIDMzZjhlNDFkNGU1MzY0Mw==')
            ->setAddressLine1('22 Mingle Lane')
            ->setAddressLine2('Great Shelford')
            ->setCity('Cambridge')
            ->setPostcode('CB22 5BG');

        foreach ($properties as $property) {
            $property->setPublished(true);
            $property->setCountryCode('UK');
            $this->propertyHelper->generateSlug($property);
            $manager->persist($property);
        }

        $manager->flush();

        return $properties;
    }

    /**
     * @return User[]
     */
    private function loadUsers(ObjectManager $manager): array
    {
        $data = [
            ['email' => self::USER_1, 'password' => 'To_The_Moon_2020'],
            ['email' => self::USER_2, 'password' => 'Fire_Dragon_2020'],
            ['email' => self::USER_3, 'password' => 'Ride_A_Bicycle_2020'],
            ['email' => self::USER_4, 'password' => 'South_Tyrol_2020'],
        ];

        $users = [];
        foreach ($data as $row) {
            $user = (new User())->setEmail($row['email']);
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $row['password']));
            $manager->persist($user);
            $users[$row['email']] = $user;
        }

        $manager->flush();

        return $users;
    }
}
