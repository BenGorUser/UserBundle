# Create custom subscriber

To create a event subscriber you just need to create a class implementing `UserEventSubscriber` interface.

Once you have created the class register it as a Symfony service and tag it replacing `your_user_type` by
your user type in tag name and `subscribes_to` with a fully qualified name of the event class you are expecting:

```yml
# app/config/services.yml

app.user.event.my_event_subscriber:
    class: AppBundle\Event\MyEventSubscriber
    arguments: ~ 
    tags:
        - { name: bengor_user_your_user_type_event_subscriber, subscribes_to: AppBundle\Event\MyEvent }
```

- Back to the [index](index.md).
