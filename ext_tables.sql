CREATE TABLE tx_mailchimpsubscribe_domain_model_subscription (
uid int(11) NOT NULL auto_increment,
pid int(11) DEFAULT '0' NOT NULL,
email text,
PRIMARY KEY (uid),
KEY parent (pid)
);
