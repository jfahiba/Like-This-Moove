<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/26/14
 * Time: 7:19 PM
 */

namespace LTM\PortailBundle\EventListener;


use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Doctrine\ORM\EntityManager;
use LTM\PortailBundle\Entity\CalendarEvent as LTMCalendarEvent;
use Symfony\Component\Security\Core\SecurityContext;

class CalendarEventListener
{
    private $entityManager;

    public function __construct(EntityManager $entityManager, $container, SecurityContext $security)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->security = $security;
    }

    public function loadEvents(CalendarEvent $calendarEvent)
    {

        $startDate = $calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();

        // The original request so you can get filters from the calendar
        // Use the filter in your query for example

        $request = $calendarEvent->getRequest();
        //$filter = $request->get('filter');

        $user = $this->entityManager->find('PortailBundle:User', $this->security->getToken()->getUser()->getId());

        if (!$user)
        {
            return;
        }

        // load events using your custom logic here,
        // for instance, retrieving events from a repository

        $qb = $this->entityManager->getRepository('PortailBundle:CalendarEvent')->createQueryBuilder('v');

        /*$qb->andWhere('v.start BETWEEN :startDate and :endDate')
            ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
            ->setParameter('endDate', $endDate->format('Y-m-d H:i:s'));
        */

        $qb->andWhere(':tag MEMBER OF v.participant')
          ->setParameter('tag', $user);
         $ltmEvents = $qb->getQuery()->getResult();

        // $companyEvents and $companyEvent in this example
        // represent entities from your database, NOT instances of EventEntity
        // within this bundle.
        //
        // Create EventEntity instances and populate it's properties with data
        // from your own entities/database values.

        foreach($ltmEvents as $ltmEvent) {
            ///$ltmEvent = new Event();

            // create an event with a start/end time, or an all day event
            if ($ltmEvent->getAllDay() === false) {
                $eventEntity = new EventEntity($ltmEvent->getTitle(), $ltmEvent->getStart(), $ltmEvent->getEnd());
            } else {
                $eventEntity = new EventEntity($ltmEvent->getTitle(), $ltmEvent->getStart(), null, true);
            }

            //optional calendar event settings
            /*$eventEntity->setAllDay(true); // default is false, set to true if this is an all day event
            $eventEntity->setBgColor('#FF0000'); //set the background color of the event's label
            $eventEntity->setFgColor('#FFFFFF'); //set the foreground color of the event's label*/

            //$url = $this->container->get('router')->generate('ltm_calendarevent_supprimer', array('id', $ltmEvent->getId()));
            //$eventEntity->setUrl($url); // url to send user to when event label is clicked
            //$eventEntity->setCssClass('icon-pencil'); // a custom class you may want to apply to event labels

            //finally, add the event to the CalendarEvent for displaying on the calendar
            $calendarEvent->addEvent($eventEntity);
        }
    }
}