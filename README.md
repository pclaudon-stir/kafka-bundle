Symfony Kafka Bundle
====================

[![Scrutinizer Code Quality][scrutinizer-code-quality-image]][scrutinizer-code-quality-link]
[![Code Coverage][code-coverage-image]][code-coverage-link]
[![Total Downloads][downloads-image]][package-link]
[![Latest Stable Version][stable-image]][package-link]
[![License][license-image]][license-link]

How to use
----------
* Install package
```bash
composer req symfony-bundles/kafka-bundle
```

* Create consumer. Example (src/Consumer/EmailSendConsumer.php):
```php
<?php

namespace App\Consumer;

use Swift_Mailer;
use SymfonyBundles\KafkaBundle\Command\Consumer;

class EmailSendConsumer extends Consumer
{
    public const QUEUE_NAME = 'email_send_queue';

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @required
     *
     * @param Swift_Mailer $mailer
     */
    public function setMailer(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    protected function onMessage(array $data): void
    {
        $message = (new \Swift_Message($data['subject']))
            ->setFrom($data['sender'])
            ->setTo($data['recipient'])
            ->setBody($data['body']);

        $this->mailer->send($message);
    }
}
```

* Produce message. Example (src/Service/EmailService.php):
```php
<?php

namespace App\Service;

use App\Consumer\DemoConsumer;
use SymfonyBundles\KafkaBundle\DependencyInjection\Traits\ProducerTrait;

class EmailService
{
    use ProducerTrait;

    /**
     * @param array $data
     */
    public function send(array $data): void
    {
        $this->producer->send(DemoConsumer::QUEUE_NAME, $data);
    }
}
```

* Run consumer. Example:
```bash
php bin/console app:consumer:email-send
```

Default configuration
---------------------
``` yml
# config/packages/sb_kafka.yaml
sb_kafka:
    producers:
        configuration:
            group.id: 'main_group'
            log.connection.close: 'false'
            metadata.broker.list: '%env(KAFKA_BROKERS)%'
            queue.buffering.max.messages: 100000

    consumers:
        configuration:
            group.id: 'main_group'
            auto.offset.reset: 'smallest'
            log.connection.close: 'false'
            metadata.broker.list: '%env(KAFKA_BROKERS)%'
```

Read more about supported configuration properties: [librdkafka configuration][librdkafka-configuration-link].

[package-link]: https://packagist.org/packages/symfony-bundles/kafka-bundle
[license-link]: https://github.com/symfony-bundles/kafka-bundle/blob/master/LICENSE
[license-image]: https://poser.pugx.org/symfony-bundles/kafka-bundle/license
[stable-image]: https://poser.pugx.org/symfony-bundles/kafka-bundle/v/stable
[downloads-image]: https://poser.pugx.org/symfony-bundles/kafka-bundle/downloads
[code-coverage-link]: https://scrutinizer-ci.com/g/symfony-bundles/kafka-bundle/?branch=master
[code-coverage-image]: https://scrutinizer-ci.com/g/symfony-bundles/kafka-bundle/badges/coverage.png?b=master
[scrutinizer-code-quality-link]: https://scrutinizer-ci.com/g/symfony-bundles/kafka-bundle/?branch=master
[scrutinizer-code-quality-image]: https://scrutinizer-ci.com/g/symfony-bundles/kafka-bundle/badges/quality-score.png?b=master
[librdkafka-configuration-link]: https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md
