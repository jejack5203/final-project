CREATE DATABASE `final_project`;

CREATE TABLE `originalMessage`
(
    `id`        int(11) NOT NULL AUTO_INCREMENT,
    `originalText`      Text NOT NULL,
    primary key (`id`)

);

CREATE TABLE `corporateMessage`
(
    `id`        int(11) NOT NULL AUTO_INCREMENT,
    `newText`   Text NOT NULL,
--     foreign key
    `originalMessageID` int(11) NOT NULL,
    primary key (`id`)
);

insert into originalMessage (originalText)
values ('Give me an excuse for being late');

INSERT INTO corporateMessage (newText, originalMessageID) VALUES ('Apologies for the delay — I encountered an unforeseen scheduling conflict that required immediate attention. I appreciate your patience and understanding.', 1);


insert into originalMessage (originalText)
values ('How to tell my boss I need a raise');

insert into corporateMessage(newText, originalMessageID)
values ('I’d appreciate the opportunity to discuss my current compensation in relation to the scope of my responsibilities and recent contributions. I’m confident that a conversation around aligning my role’s impact with appropriate compensation would be mutually beneficial.', 2);

insert into originalMessage(originalText)
values ('How to tell someone that is not my job');

insert into corporateMessage(newText, originalMessageID)
values ('That falls outside the scope of my current responsibilities, but I’m happy to collaborate or direct it to the appropriate channel.', 3);
