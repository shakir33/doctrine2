<?php

namespace Doctrine\Tests\ORM\Functional\Ticket;

require_once __DIR__ . '/../../../TestInit.php';

class DDC881Test extends \Doctrine\Tests\OrmFunctionalTestCase
{

    protected function setUp()
    {
        parent::setUp();

        try {
            $this->_schemaTool->createSchema(array(
                $this->_em->getClassMetadata(__NAMESPACE__ . '\DDC881User'),
                $this->_em->getClassMetadata(__NAMESPACE__ . '\DDC881Phonenumber'),
                $this->_em->getClassMetadata(__NAMESPACE__ . '\DDC881Phonecall'),
            ));
        } catch (\Exception $e) {

        }
    }

    /**
     * @group DDC-117
     * @group DDC-881
     */
    public function testIssue()
    {
        /* Create two test users: albert and alfons */
        $albert = new DDC881User;
        $albert->setName("albert");
        $this->_em->persist($albert);

        $alfons = new DDC881User;
        $alfons->setName("alfons");
        $this->_em->persist($alfons);

        $this->_em->flush();

        /* Assign two phone numbers to each user */
        $phoneAlbert1 = new DDC881PhoneNumber();
        $phoneAlbert1->setUser($albert);
        $phoneAlbert1->setId(1);
        $phoneAlbert1->setPhoneNumber("albert home: 012345");
        $this->_em->persist($phoneAlbert1);

        $phoneAlbert2 = new DDC881PhoneNumber();
        $phoneAlbert2->setUser($albert);
        $phoneAlbert2->setId(2);
        $phoneAlbert2->setPhoneNumber("albert mobile: 67890");
        $this->_em->persist($phoneAlbert2);

        $phoneAlfons1 = new DDC881PhoneNumber();
        $phoneAlfons1->setId(1);
        $phoneAlfons1->setUser($alfons);
        $phoneAlfons1->setPhoneNumber("alfons home: 012345");
        $this->_em->persist($phoneAlfons1);

        $phoneAlfons2 = new DDC881PhoneNumber();
        $phoneAlfons2->setId(2);
        $phoneAlfons2->setUser($alfons);
        $phoneAlfons2->setPhoneNumber("alfons mobile: 67890");
        $this->_em->persist($phoneAlfons2);

        /* We call alfons and albert once on their mobile numbers */
        $call1 = new DDC881PhoneCall();
        $call1->setPhoneNumber($phoneAlfons2);
        $this->_em->persist($call1);

        $call2 = new DDC881PhoneCall();
        $call2->setPhoneNumber($phoneAlbert2);
        $this->_em->persist($call2);

        $this->_em->flush();
    }

}

/**
 * @Entity
 */
class DDC881User
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @Column(type="string")
     */
    private $name;
    /**
     * @OneToMany(targetEntity="DDC881PhoneNumber",mappedBy="id")
     */
    private $phoneNumbers;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}

/**
 * @Entity
 */
class DDC881PhoneNumber
{

    /**
     * @Id
     * @Column(type="integer")
     */
    private $id;
    /**
     * @Id
     * @ManyToOne(targetEntity="DDC881User",cascade={"all"})
     */
    private $user;
    /**
     * @Column(type="string")
     */
    private $phonenumber;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUser(DDC881User $user)
    {
        $this->user = $user;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phonenumber = $phoneNumber;
    }

}

/**
 * @Entity
 */
class DDC881PhoneCall
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @OneToOne(targetEntity="DDC881PhoneNumber",cascade={"all"})
     * @JoinColumns({
     *  @JoinColumn(name="phonenumber_id", referencedColumnName="id"),
     *  @JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    private $phonenumber;
    /**
     * @Column(type="string",nullable=true)
     */
    private $callDate;

    public function setPhoneNumber(DDC881PhoneNumber $phoneNumber)
    {
        $this->phonenumber = $phoneNumber;
    }

}