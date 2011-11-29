ALTER TABLE `platforms`
ADD  `developer` text,
ADD  `manufacturer` text,
ADD  `media` text,
ADD  `cpu` text,
ADD  `memory` text,
ADD  `graphics` text,
ADD  `sound` text,
ADD  `maxcontrollers` text,
ADD  `display` text,
ADD  `overview` text,
ADD  `youtube` varchar(255) DEFAULT NULL;