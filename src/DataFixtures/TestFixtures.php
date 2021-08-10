<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\City;
use App\Entity\Comment\TenancyReviewComment;
use App\Entity\District;
use App\Entity\Locale\CityLocale;
use App\Entity\Locale\DistrictLocale;
use App\Entity\Locale\Locale;
use App\Entity\Property;
use App\Entity\Review\LocaleReview;
use App\Entity\Survey\Choice;
use App\Entity\Survey\Question;
use App\Entity\Survey\Survey;
use App\Entity\TenancyReview;
use App\Entity\TenancyReviewSolicitation;
use App\Entity\User;
use App\Entity\Vote\TenancyReviewVote;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TestFixtures extends AbstractDataFixtures
{
    public const TEST_USER_STANDARD_EMAIL = 'test.user.standard@starsol.co.uk';
    public const TEST_USER_AGENCY_1_ADMIN_EMAIL = 'test.agency.1.admin@starsol.co.uk';
    public const TEST_USER_AGENCY_2_ADMIN_EMAIL = 'test.agency.2.admin@starsol.co.uk';

    public const TEST_AGENCY_1_SLUG = 'testerton';
    public const TEST_AGENCY_2_SLUG = 'checkerfield';
    public const TEST_BRANCH_101_SLUG = 'branch101slug';
    public const TEST_BRANCH_102_SLUG = 'branch102slug';
    public const TEST_BRANCH_201_SLUG = 'branch201slug';
    public const TEST_CITY_KINGS_LYNN_SLUG = '8475b53127850aba';
    public const TEST_CITY_LOCALE_KINGS_LYNN_SLUG = 'test-kl-city-locale';
    public const TEST_DISTRICT_ISLINGTON_SLUG = 'f9a1d092051730ae';
    public const TEST_LOCALE_SLUG = 'fakenham';
    public const TEST_REVIEW_SLUG_1 = 'review-1-slug';
    public const TEST_PROPERTY_1_SLUG = 'property-1-slug';
    public const TEST_PROPERTY_2_SLUG = 'property-2-slug';
    public const TEST_PROPERTY_3_SLUG = 'property-3-slug';
    public const TEST_PROPERTY_4_SLUG = 'property-4-slug';
    public const TEST_PROPERTY_5_SLUG = 'property-5-slug';
    public const TEST_REVIEW_SOLICITATION_CODE = '73d2d50d17e8c1bbb05b8fddb3918033f2daf589';
    public const TEST_SURVEY_SLUG = 'test-survey';

    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    protected function getEnvironments(): array
    {
        return ['test', 'e2e'];
    }

    protected function doLoad(ObjectManager $manager): void
    {
        $this->loadCities($manager);
        $this->loadDistricts($manager);

        $user1 = (new User())
            ->setEmail(self::TEST_USER_STANDARD_EMAIL)
            ->setTitle('Mr')
            ->setFirstName('Terry')
            ->setLastName('Sterling')
            ->setIsVerified(true);

        $user1->setPassword($this->userPasswordEncoder->encodePassword($user1, 'Password1'));
        $manager->persist($user1);

        $user2 = (new User())
            ->setEmail(self::TEST_USER_AGENCY_1_ADMIN_EMAIL)
            ->setTitle('Ms')
            ->setFirstName('Fiona')
            ->setLastName('Dutton')
            ->setIsVerified(true);

        $user2->setPassword($this->userPasswordEncoder->encodePassword($user2, 'Password2'));
        $manager->persist($user2);

        $user3 = (new User())
            ->setEmail(self::TEST_USER_AGENCY_2_ADMIN_EMAIL)
            ->setTitle('Ms')
            ->setFirstName('Ruth')
            ->setLastName('Pound')
            ->setIsVerified(true);

        $user3->setPassword($this->userPasswordEncoder->encodePassword($user3, 'Password3'));
        $manager->persist($user3);

        $branch101 = (new Branch())
            ->setName('Dereham')
            ->setPublished(true)
            ->setSlug(self::TEST_BRANCH_101_SLUG)
        ;
        $manager->persist($branch101);

        $branch102 = (new Branch())
            ->setName('Guist')
            ->setPublished(true)
            ->setSlug(self::TEST_BRANCH_102_SLUG)
        ;
        $manager->persist($branch102);

        $branch201 = (new Branch())
            ->setName('Reepham')
            ->setPublished(true)
            ->setSlug(self::TEST_BRANCH_201_SLUG)
        ;
        $manager->persist($branch201);

        $agency1 = (new Agency())
            ->setName('Testerton Lettings')
            ->setPublished(true)
            ->setSlug(self::TEST_AGENCY_1_SLUG)
            ->addBranch($branch101)
            ->addBranch($branch102)
            ->addAdminUser($user2)
        ;
        $manager->persist($agency1);

        $agency2 = (new Agency())
            ->setName('Checkerfield Homes')
            ->setPublished(true)
            ->setSlug(self::TEST_AGENCY_2_SLUG)
            ->addBranch($branch201)
            ->addAdminUser($user3)
        ;
        $manager->persist($agency2);

        $property1 = (new Property())
            ->setAddressLine1('Testerton Hall')
            ->setPostcode('NR21 7ES')
            ->setCountryCode('UK')
            ->setSlug(self::TEST_PROPERTY_1_SLUG);
        $manager->persist($property1);

        /** @var City $kingsLynn */
        $kingsLynn = $this->getReference('city-kings-lynn');

        $property2 = (new Property())
            ->setAddressLine1('Callisto Cottage')
            ->setAddressLine2('Lynn Road')
            ->setPostcode('PE31 8RP')
            ->setCity($kingsLynn)
            ->setAddressDistrict("King's Lynn And West Norfolk")
            ->setThoroughfare('Lynn Road')
            ->setCountryCode('UK')
            ->setSlug(self::TEST_PROPERTY_2_SLUG);
        $manager->persist($property2);

        $property3 = (new Property())
            ->setAddressLine1("43 Duke's Yard")
            ->setAddressLine2('Lynn Road')
            ->setPostcode('PE31 8RP')
            ->setCity($kingsLynn)
            ->setAddressDistrict("King's Lynn And West Norfolk")
            ->setThoroughfare('Lynn Road')
            ->setCountryCode('UK')
            ->setSlug(self::TEST_PROPERTY_3_SLUG);
        $manager->persist($property3);

        $property4 = (new Property())
            ->setAddressLine1('Lysithea Lodge')
            ->setAddressLine2('Lynn Road')
            ->setPostcode('PE31 8RP')
            ->setCity($kingsLynn)
            ->setAddressDistrict("King's Lynn And West Norfolk")
            ->setThoroughfare('Lynn Road')
            ->setCountryCode('UK')
            ->setSlug(self::TEST_PROPERTY_4_SLUG);
        $manager->persist($property4);

        $property5 = (new Property())
            ->setAddressLine1('19 St Botolphs Cloverfield, Gwent')
            ->setPostcode('CF32 8HG')
            ->setCountryCode('UK')
            ->setSlug(self::TEST_PROPERTY_5_SLUG);
        $manager->persist($property5);

        $comment = (new TenancyReviewComment())
            ->setPublished(true)
            ->setUser($user2)
            ->setContent('Hello Terrence! The mushrooms that were growing in the garden have been removed.')
        ;
        $manager->persist($comment);

        /** @var TenancyReviewComment $tenancyReviewComment */
        $tenancyReviewComment = $comment;

        $positiveVote1 = (new TenancyReviewVote())
            ->setPositive(true)
            ->setUser($user2);
        $manager->persist($positiveVote1);
        /** @var TenancyReviewVote $positiveReviewVote1 */
        $positiveReviewVote1 = $positiveVote1;

        $positiveVote2 = (new TenancyReviewVote())
            ->setPositive(true)
            ->setUser($user3);
        $manager->persist($positiveVote2);
        /** @var TenancyReviewVote $positiveReviewVote2 */
        $positiveReviewVote2 = $positiveVote2;

        $tenancyReview = (new TenancyReview())
            ->setUser($user1)
            ->setProperty($property1)
            ->setBranch($branch101)
            ->setTitle('What a lovely cupboard under the stairs')
            ->setAuthor('Terrence S.')
            ->setContent(
                'Just wow, the cupboard under the stairs led to a basement where the was a dragon guarding '
                .'a UFO. Eventually I talked the dragon into letting me take the UFO outside. I did and I flew it '
                .'to Fakenham.'
            )
            ->setStart(new DateTime('2007-09-01'))
            ->setEnd(new DateTime('2020-10-01'))
            ->setOverallStars(5)
            ->setAgencyStars(null)
            ->setLandlordStars(null)
            ->setPropertyStars(5)
            ->setPublished(true)
            ->addComment($tenancyReviewComment)
            ->addVote($positiveReviewVote1)
            ->addVote($positiveReviewVote2)
        ;
        $manager->persist($tenancyReview);

        $rs = (new TenancyReviewSolicitation())
            ->setBranch($branch101)
            ->setSenderUser($user2)
            ->setProperty($property1)
            ->setRecipientFirstName('Anna')
            ->setRecipientLastName('Testinova')
            ->setRecipientEmail('anna.testinova@starsol.co.uk')
            ->setCode(self::TEST_REVIEW_SOLICITATION_CODE)
        ;

        $manager->persist($rs);

        $this->loadSurvey($manager);
        $this->loadLocale($manager, $tenancyReview);

        $manager->flush();
    }

    private function loadSurvey(ObjectManager $manager): void
    {
        $question1 = (new Question())
            ->setType('free')
            ->setContent('How does a Snickers make you feel?')
            ->setHelp('Maybe less hungry.')
            ->setSortOrder(1)
        ;

        $question2 = (new Question())
            ->setType('choice')
            ->setContent('Where do you normally buy chocolate bars?')
            ->setSortOrder(2)
            ->addChoice((new Choice())->setName('Supermarket'))
            ->addChoice((new Choice())->setName('Fuel station'))
            ->addChoice((new Choice())->setName('Newsagent'))
            ->addChoice((new Choice())->setName('Sweet shop'))
        ;

        $question3 = (new Question())
            ->setType('scale5')
            ->setContent('How likely are you share a Twix with someone else?')
            ->setHelp('There are two individual bars in a Twix')
            ->setHighMeaning('Very likely')
            ->setLowMeaning('Very unlikely')
            ->setSortOrder(3)
        ;

        $survey = (new Survey())
            ->setSlug(self::TEST_SURVEY_SLUG)
            ->setTitle('Chocolate bars of the UK')
            ->setDescription('Your thoughts on the options')
            ->setPublished(true)
            ->addQuestion($question1)
            ->addQuestion($question2)
            ->addQuestion($question3)
        ;

        $manager->persist($survey);
    }

    private function loadLocale(ObjectManager $manager, TenancyReview $tenancyReview): void
    {
        $locale = (new Locale())
            ->setName('Fakenham')
            ->setSlug('fakenham')
            ->setPublished(true)
            ->addTenancyReview($tenancyReview)
        ;
        $manager->persist($locale);

        $localeReview = (new LocaleReview())
            ->setLocale($locale)
            ->setAuthor('Bumblebee Man')
            ->setTitle('This place exists')
            ->setContent('Test review content')
            ->setOverallStars(4)
            ->setSlug(self::TEST_REVIEW_SLUG_1)
            ->setPublished(true)
        ;

        $manager->persist($localeReview);

        /** @var City $kingsLynnCity */
        $kingsLynnCity = $this->getReference('city-kings-lynn');

        $cityLocale = (new CityLocale())
            ->setCity($kingsLynnCity)
            ->setName("King's Lynn")
            ->setSlug(self::TEST_CITY_LOCALE_KINGS_LYNN_SLUG)
            ->setPublished(true)
        ;
        $manager->persist($cityLocale);

        /** @var District $islingtonDistrict */
        $islingtonDistrict = $this->getReference('district-islington');

        $districtLocale = (new DistrictLocale())
            ->setDistrict($islingtonDistrict)
            ->setName('Islington')
            ->setSlug('test-district-locale-slug')
            ->setPublished(true)
        ;
        $manager->persist($districtLocale);
    }

    private function loadCities(ObjectManager $manager): void
    {
        $cambridge = (new City())
            ->setName('Cambridge')
            ->setCounty('Cambridgeshire')
            ->setSlug('test-city-slug-cambridge')
            ->setCountryCode('UK')
        ;

        $kingsLynn = (new City())
            ->setName("King's Lynn")
            ->setCounty('Norfolk')
            ->setSlug(self::TEST_CITY_KINGS_LYNN_SLUG)
            ->setCountryCode('UK')
        ;

        $manager->persist($cambridge);
        $manager->persist($kingsLynn);

        $this->addReference('city-cambridge', $cambridge);
        $this->addReference('city-kings-lynn', $kingsLynn);
    }

    private function loadDistricts(ObjectManager $manager): void
    {
        $cambridge = (new District())
            ->setName('Cambridge')
            ->setCounty('Cambridgeshire')
            ->setSlug('test-district-slug-1')
            ->setCountryCode('UK')
        ;

        $eastCambridgeshire = (new District())
            ->setName('East Cambridgeshire')
            ->setCounty('Cambridgeshire')
            ->setSlug('test-district-slug-2')
            ->setCountryCode('UK')
        ;

        $islington = (new District())
            ->setName('Islington')
            ->setSlug(self::TEST_DISTRICT_ISLINGTON_SLUG)
            ->setCountryCode('UK')
        ;

        $kingsLynn = (new District())
            ->setName("King's Lynn And West Norfolk")
            ->setCounty('Norfolk')
            ->setSlug('test-district-slug-4')
            ->setCountryCode('UK')
        ;

        $manager->persist($cambridge);
        $manager->persist($eastCambridgeshire);
        $manager->persist($islington);
        $manager->persist($kingsLynn);

        $this->addReference('district-cambridge', $cambridge);
        $this->addReference('district-east-cambridgeshire', $eastCambridgeshire);
        $this->addReference('district-islington', $islington);
        $this->addReference('district-kings-lynn', $kingsLynn);
    }
}
