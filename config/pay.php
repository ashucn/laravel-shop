<?php

return [
    'alipay' => [
        'app_id'         => '2016092400585070',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuj5DEtJ+GFUYamyl8p/33yBH2CGvlmEj0Lc8pq2SKvmc0U39cLa2OG8cN1TWHsWLfOIvAwpGnHyIDQQjQrpA48Bq3AEqDtZFDKH2TMrXIq8QsxT7q3B+K5DGwCDcc37IV9tZldb6fr4EVuRbuhn4wsFZ22DFy+GbSm0mCwC7R/+lf4Bt4WglnxTLbwkNAaLahqFnkBFipshHa9gXW9AwFVyPn8NmSx+FntKDEEJ5pR/L8jQqSCcZJZqpV9d/DsZ4+GTwsoVDWkqjynxvs+PldUERudhd+WumzpUq86ENrsLJXrmEZ9Nq4XaBgLFxM4EHyNIAtWJ5pSwo0b605Mwb5QIDAQAB',
        'private_key'    => 'MIIEowIBAAKCAQEAoAwEM0z83i1AhAYcbCjuxfTiel3gFsot8gPz07pZToBY6AAfIxNCb5uZtSTRlReQZ3eVZUkbnLs/diQ8MWVZ7lgmCKMnXdYx7GAgIKXmL6rcia3QOO8O9ocdemImd9Vb4cUnk0uw0qspRhztbq315sAyodS+frbSo7FgAymM80mb9WZ6016jnhF86hYo4O1o8GazA/52yOH1W+44nlgc6f1z86htg9n8xXQ2xeKJHkceFOeEDYDgSoAq3UJxNihzxeDJrUgU9cpKzLjOhHQUPKzQAhVLi1EV+mDcAC+Y5YN+XcWFltSDAHtbSoiRhgfK7whFu4bLWoPv8k6bFRRfVQIDAQABAoIBABBVyaP2yqRemAQhn4sGjoBT4z28ESAiWJgoAR3uGilh/jEHuiXZVGGRzxlm/aEq+4kj8nKZCKFxlyOWxTIBdzFVe+RLyrvQHd5TXVQXuqvI06OVwvfcpnlRBxgzplt6wlsDxWtKQWzvHVJTp4QxazS3DzPnQ94pCMy+UN9hwUofYuBd6LKw8tgWo11CevZ3IDK8KFgEGhLABPY5+7voGQhyyl3orLSMg9RPQgj9yoPv25nxhRgbhzzg4wfPa3qEBvDeiB6F/gMHsicyVkW0jAo9EviPGH36oVK93acLDVEK3GlSU5ODXJboAQr3T4Ucr19o/pjDHMsurkBCba3S7iECgYEAztnw35QZU9cIVxPIiqtfNXv12p1tdsrfs8Os1Cw6P28BjxhRR666uXE5LwRP1+TeTA93b7hgnB0/7Psm6pQlPS6XlaFbMx7Zx6hFkwGDo5qMVNUKGc+mGowlX4hkx9R5hvptOXpj0N2u0z5w0TW3/czRoM1CzXhRUg0vYIAydPkCgYEAxhMf1j1BgJKVvbQYGwP9eE+l4xJjwmFb3Iw9fXrPkDoeunaRt+hJsSMzekF75rNKc0SU5Tv1KLhnA7jBuq9ZnQxq9Z+JDIMFSW7BU8YwjhKQpdMkB9xitqRXaNaGt6aFkR5tbWAbbCBnRCd7WqXNlQYIB2nRbElScTvvRtGigD0CgYBCVcAaYyswKOCC1FRWrBiPVV6FqkDFfM/6nUDalghj+VxdoGXIgC9fcwDspAxa5wmnCLq7uDw7NGIgxHY5eiLrGPsBwEo3/afHkJ4nQ7AMkm54cg2YM3BISTNfDKWNNAV5FOxcJ3TFMaJ0Gi0h2oFTwhBj6g+HRxOFJbdQ0ivcOQKBgArH2bDAbsM/tzS3C6TUx8P3mdDNHG7k+6n6XBJrT8bK0wdVEsNOukwQ3WlnDQZWddPmRLBhXguUVc8HDExL5PW+SkgKIdxzeSMxCwazP8tLyWGtJ0XiQeEs+rDzFCwEvTpTzKLqWzO8Y8J636TkZ1cYF/HHWaWqK/mLyI8zM8QFAoGBAJ2QGB4e4IC5C+V7s62XxQo6QqIoR1PYyt7XkyEdJlS75v/au5uMSQVv3EFzR3MPWqsQqn74m8DnHzOdzGxg5180RQH5NpiUoQDNyrIs98el7/IYG1X9a2ShdTMJcyra9H1WftSSrlasa8d7jhPJV5KHZ3FA2sOKmeCa28b/JxXH',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
