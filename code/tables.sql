DROP TABLE IF EXISTS contacts;
CREATE TABLE contacts
(
  contactID            smallint unsigned NOT NULL auto_increment,
  contactName          varchar(255) NOT NULL,                      # Full title of the article
  contactRelation      text NOT NULL,                              # A short summary of the article
  contactNumber 	   text NOT NULL,                        # The HTML content of the article

  PRIMARY KEY     (contactID)
);

DROP TABLE IF EXISTS guardians;
CREATE TABLE guardians
(
  guardianID            smallint unsigned NOT NULL auto_increment,
  guardianName          varchar(255) NOT NULL,                    
  guardianRelation      char(30) NOT NULL,                              
  guardianCell 	   char(30),  
  guardianHome 	   char(30),  
  guardianWork 	   char(30),  
  guardianEmail 	   char(30) NOT NULL,  
  guardianAddress 	   varchar(255),  
  guardianCity 	   char(30),  
  guardianProvince 	   char(30),  
  guardianCountry 	   char(30),    

  PRIMARY KEY     (guardianID)
);

DROP TABLE IF EXISTS participants;
CREATE TABLE participants
(
  participantID            smallint unsigned NOT NULL auto_increment,
  participantName          varchar(255) NOT NULL,                      
  participantDOB      	   date NOT NULL,                             
  participantGender 	   char(30) NOT NULL, 
  participantSupp		   boolean NOT NULL,
  participantNeeds		   mediumtext,	

  PRIMARY KEY     (participantID)
);

DROP TABLE IF EXISTS programs;
CREATE TABLE programs
(
  programID            smallint unsigned NOT NULL auto_increment,
  programName          varchar(255) NOT NULL,                      # Full title of the article
  programStart      date NOT NULL,                              # A short summary of the article
  programFrequency 	   smallint NOT NULL,                        # The HTML content of the article

  PRIMARY KEY     (programID)
);

DROP TABLE IF EXISTS PartProg
CREATE TABLE PartProg
(
PP_ID smallint unsigned NOT NULL auto_increment,
programID smallint NOT NULL,
participantID smallint,
PRIMARY KEY (PP_ID),
FOREIGN KEY (participantID) REFERENCES Persons(P_Id)
)