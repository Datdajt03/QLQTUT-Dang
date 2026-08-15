# International Journal of Evaluation and Research in Education (IJERE)

- **Original File:** `Web_Based_Information_System_Design_in_Elementary_.pdf`
- **Total Pages:** 7
- **Title (Metadata):** International Journal of Evaluation and Research in Education (IJERE)
- **Author (Metadata):** IJERE
- **Creation Date:** D:20221113053159+00'00'

---

## Page 1

Journal of ICT Aplication and System (JICTAS) 
Volume 1 Number 2, Desember 2022, pp. 62 ~68 
ISSN: 2830-098X, DOI: 10.56313/jictas.v1i2.163 
              
 
                                     62 
 
Journal homepage: https://e-jurnal.rokania.ac.id/index.php/jictas/ 
Web Based Information System Design in Elementary Schools 
 
 
Erliyen Nofrianda1, Detri Amelia Chandra2, Andri febriansyah3, Anik Supriani4 
Program Studi Pendidikan Teknologi Informasi – STKIP Rokania 1,2,3,4 
 
 
Article Info 
 
ABSTRACT (10 PT) 
Article history: 
Received July 26, 2022 
Revised Agustus 06, 2022 
Accepted November 13, 2022 
 
 
This article aims to design and implement a Web-Based Information System 
in Elementary Schools. The method used in this research is to use the system 
development method with the Waterfall model. The type of data in this study 
is to use qualitative data. Web-Based Information System in Elementary 
School is designed using the UML (Unified Modeling Language) 
development method which consists of Use Case Diagrams, Activity 
Diagrams, Sequence Diagrams and Class Diagrams. Database design using 
MySQL and system interface. The software used in the design and 
implementation of the system uses XAMPP as a web server, PHP and MySQL 
as a database. Web-Based Information System in elementary schools includes, 
login page, home page, profile page, student page, gallery, agenda page, 
student registration page, contact page, manage profile page, manage student 
page, manage gallery page, manage agenda page, page registration 
Keywords: 
Information System 
Web 
Waterfall 
UML  
 
This is an open access article under the CC BY-SA license. 
 
Corresponding Author: 
Erliyen Nofrianda  
Information Technology Education Study Program, STKIP Rokania 
Jl. Raya Pasir Pengaraian Km 15, Rokan Hulu, Riau, Indonesia 
Email: erliennovrianda4@gmail.com 
 
 
1. 
INTRODUCTION (10 PT) 
The rapid development of information technology has spurred the emergence of various new 
applications, including in the field of information technology. The development of information technology 
today has grown very rapidly which has brought us into a new world, a world where communication plays an 
important role in life. Various kinds of facilities are provided to fulfill all communication needs. The website 
is one of the historic discoveries in the field of Internet technology-based information. The website is expected 
to be an alternative for developing more effective and efficient information systems at low costs in the future. 
This can run smoothly if there is a computer network as an internet medium. 
The education sector, especially schools, does a lot of data processing, both data for teachers, students, 
and staff. School data is processed in large quantities, can change at any time so that storage media must be 
carried out properly and always updated on an ongoing basis. Most schools still manage academic data 
manually, but some have used computers but have not used the internet. A system like this, of course, has many 
shortcomings that affect the weakness of wasted energy, administrative systems, academic services that are 
less than optimal 
System design is the determination of the processes and data required by the new system. If the system 
is computer based, the design may include specifications of the equipment to be used. (Sukisno & Wuni, 2017). 
Information is a collection of data that is processed into a form that is more useful and more meaningful to the 
recipient. Meanwhile, according to (Hardiyanti, 2021) information is a collection of facts (data) that are 
organized in a certain way so that they have meaning for the recipient. A system will not run smoothly and can 
eventually die without any information. 
This information system is needed because it includes all activities to process, collect, store, analyze 
and disseminate data that is processed into information for specific purposes. So that the data that has been 


## Page 2

JICTAS  
ISSN: 2830-098X 
 
 
Web-Based Information System Design in Elementary Schools  (Erliyen Nofrianda) 
63 
processed in such a way can produce an understanding that is right on target for anyone who needs the 
information. 
Schools are educational institutions where students and teachers carry out teaching and learning 
activities, in this case the school can provide school information to people who need information, especially 
parents of students. School information needed by parents and the general public is conventionally done by 
visiting the school, then asking the school for the information needed and asking for information from students 
who attend the school in question to get the information needed. However, because of the website, parties who 
need information about the school do not need to come to school and ask students but simply open the school's 
website. 
 
 
2. 
METHOD (10 PT) 
This study uses the waterfall method. The Waterfall model is a sequential development model. The 
Waterfall model is systematic and sequential in building a software. The build process follows a flow from 
analysis, design, code, testing and maintenance. The waterfall development model has several advantages, 
namely it can be easily understood and can be applied in the software development process. The waterfall 
development model (Dini Silvi, 2019) can be seen in the following figure . 
 
 
 
Figure 1. Waterfall Development Model 
 
 
 
3. 
RESULTS AND DISCUSSION (10 PT) 
System analysis can be in the form of depicting, designing, and making sketches or arrangements of 
several separate elements into a unified and functioning unit, it also involves the configuration of the hardware 
and software components of a system (Amelia Chandra et al., 2021) . For the analysis and design of the system 
to be built using use case diagrams, activity diagrams, class diagrams and sequence diagrams. 
 
3.1.  Use Case Diagram 
Use case describes an interaction between one or more actors with the information system created. 
Use cases are used to find out what functions have the right to use these functions (Rahyudi, 2022) The symbols 
in the use cases can be seen in table 4.1 below: 
 
Tabel 1. Symbol Use Case Diagram 
No 
Name 
Picture 
Information 
1 
Actor 
 
Specifies the set of roles that users play when interacting 
with use cases. 
2 
Dependency 
 
Relationship where changes that occur in an independent 
element (independent) will affect elements that are not 
independent (independent) 


## Page 3

      
  
 
       ISSN: 2830-098X 
 JICTAS, Volume 1 Number 2, December 2022 : 62 -68 
64
3 
Generalication 
 
A relationship in which the child object (descendent) shares 
the behavior and data structure of the object that is above the 
parent object (ancestor) 
4 
Include 
 
Specifies that source use case explicitly 
5 
Extend 
 
Specifies that the target use case extends the behavior of the 
source use case at a given point 
6 
Association 
 
What connects one object to another. 
7 
System 
 
Specifies the package data that displays the system in a 
limited way 
 
 
The symbols used in making Use Cases in this study are the first actor symbols which describe people who 
will use the system, the Use Case symbols units which represent the menus of the system to be created, the 
third is the association symbol which is a communication link between the actor and the user . 
 
3.2.  Activity Diagram 
Activity diagrams or activity diagrams describe the workflow or activity of a system or business 
process or menu in the software, what needs to be considered here is that the activity diagram describes system 
activities not those carried out by actors, so activities are carried out by the system (Rahyudi , 2022). Activity 
diagrams are widely used to define the following : 
1)  
Business process design in which each sequence of activities described is a defined system business 
process. 
2)  
View grouping of the system where each activity is considered to have a view interface design. 
3)  
Test design in which each activity is considered to require a test that needs to be defined test cases. 
The sequence diagram can be seen in table 2 below : 
 
Tabel 2 Diagram sequence 
 
 
3.3. Class Diagram 
Class diagram is a diagram that describes the structure of the system in terms of defining the classes that 
will be made to build the system. Class is a collection of objects with and that have a common structure, 


## Page 4

JICTAS  
ISSN: 2830-098X 
 
 
Web-Based Information System Design in Elementary Schools  (Erliyen Nofrianda) 
65 
common behavior, common relations, and common semantics/words. Classes are determined/found by 
examining objects in sequence diagrams and collaboration diagrams [5] Class diagram symbols and 
descriptions can be seen in table 4.3 below : 
 
Tabel 3 Diagram Class 
Name 
Information 
Symbol 
Dependency 
The use of dependencies is used to indicate operations on a 
class that uses another class. 
 
Class 
Classes are the building blocks of object-oriented 
programming. class is described as a box divided into 3 
parts. The top is the name part of the class. The middle 
section defines the class attribute. The final section defines 
method-method 
 
Association 
An association is the most common relationship between 
two classes and is denoted by a line connecting the two 
classes. This line can represent the types of relationships 
and can also display the laws of multiplicity in a 
relationship. 
 
Composition 
If a class cannot stand alone and must be part of another 
class, then that class has a Composition relation to the class 
on which it depends. 
 
 
The user case is a general technique used in developing information systems to get functional requirements 
from existing systems. The use case diagram of a web-based information system in elementary schools can 
be seen in the following figure: 
 
 
 
Picture 2. Use Case 
The picture above explains that the public can see the menus on the website such as their menu which is 
the main menu when accessed publicly, the profile menu is a menu that provides information about elementary 
schools, student data is information about student data, teacher or staff data. , student grades and data from the 
principal in the elementary school. The gallery menu is information about photos of activities carried out by 
students and teachers in the elementary school. The agenda menu is information in the form of extracurricular 
which is the development of students' talents such as dancing, singing, storytelling, scouting, sports and other 
extracurricular activities, the registration menu is information for new students who want to register at the 


## Page 5

      
  
 
       ISSN: 2830-098X 
 JICTAS, Volume 1 Number 2, December 2022 : 62 -68 
66
school, while the contact menu is information or activities that can be used. used by the community in providing 
questions or suggestions to school staff. 
Image of staff as actors who have tasks in processing elementary school websites including logging into 
the system, viewing dashboards, managing profiles, managing student data, student grades, teacher data, 
principal data, managing galleries, managing agendas, confirming new registrations, and managing contact to 
provide information to people who want to know about the elementary school 
Activity diagrams are carried out by the admin and the system, namely the admin can login, if it fails, the 
system will display an error and return to the login page. However, if successful, the admin can manage profile 
data, student data manager, student data, teacher or staff data, school principal data, gallery data, agenda data, 
and registration data which can be seen in Figure 4.2 below : 
 
 
Picture 3. Diagram Activity Admin 
 
The web-based information system login sequence diagram in elementary schools can be seen in Figure 
4.3 below: 
 
 
Picture 4. Diagram Squence 
The login database is implemented in the form of a table where each column contains data regarding the 
database data used which can be seen in table 4 below: 
 
Tabel 4 Tabel Admin 
No 
Column 
Type 
Null 
1 
id_admin (kunci utama) 
int(11) 
Tidak 
2 
Useradmin 
Varchar(20) 
Tidak 
3 
Pasadmin 
Varchar(150) 
Tidak 
4 
Namaadmin 
Varchar(20) 
Tidak 


## Page 6

JICTAS  
ISSN: 2830-098X 
 
 
Web-Based Information System Design in Elementary Schools  (Erliyen Nofrianda) 
67 
System testing is part of the measurement that has right and wrong answers. The test is carried out using black 
box testing, black box testing is a test carried out which only observes the results of execution through test data 
and checks the functionality of the software. This test focuses on the functional requirements of the software. 
Tests are carried out in order to briefly check the accuracy of the system 
 
4. 
CONCLUSION 
Based on the results of research and discussion of web-based information systems in elementary 
schools, it can be concluded that the web-based information design is designed using the URL model, which 
includes Use Case Diagrams, Activity Diagrams, Class Diagrams and Sequence Diagrams. The web-based 
information system in elementary schools uses the PHP version 7 programming language, html as a markup 
language medium. Implementation of a web-based information system in elementary schools in the form of 
logins, dashboards, profile input, student data input, teacher or staff data input, principal data input, student 
data input, gallery data input, activity data input, agenda data input, registration data input new students and 
input contact data. The web-based information system in elementary schools was tested using black box testing 
 
ACKNOWLEDGEMENTS 
Thank you to the head of the study program who has provided motivation to carry out research to 
completion, then to the supervisor who gave criticism for the perfection of this article. 
 
 
REFERENCES 
[1] Sukisno and W. F. Wuni, “Analisa Dan Perancangan Sistem Informasi Tracking Acuan Quality 
Departemen Brushing Berbasis Web Di PT. Indotaichen Textile Industry,” J. Informatics Eng., vol. 5, 
no. 1, pp. 43–51, 2017. 
[2]  dkk Hardiyanti, “Sistem informasi sekolah berbasis web pada sekolah dasar negeri (SDN) Seriti,” 
Indones. J. Educ. Humanit., vol. 1, no. 3, pp. 156–168, 2021. 
[3] D. Dini Silvi, “Penerapan Metode Waterfall dalam Perancangan Sistem Informasi Aplikasi Bantuan 
Sosial Berbasis Android,” Semin. Nas. Sains dan Teknol. 2019, pp. 1–7, 2019. 
[4] D. Amelia Chandra et al., “Penerapan Metode Item Based Collaborative Filtering Berbasis Web Pada 
Recommender System Laptop,” Eng. Technol. Int. J. Juli, vol. 3, no. 2, pp. 2714–755, 2021. 
[5] D. Rahyudi, “Aplikasi Media Pembelajaran Sistem Fotosintesis Pada Tumbuhan Berbasis Android 
Kelas Viii Smp Negri 4 Wotu,” vol. 2, no. 1, pp. 20–33, 2022. 
[6] A. Lubis, Basis Data Dasar, 1st ed. Yogyakarta: Deepublish, 2016. 
 
BIOGRAPHIES OF AUTHORS 
 
Erliyen Nofrianda. Currently studying at the Rokania Teaching College of Education in the 
Information Technology Education Study Program, She can be contacted at email: 
erliennovrianda4@gmail.com 
 
 
Detri Amelia Chandra is a Lecture, Program Study of Educational Information 
Technology, STKIP Rokania, Raya Pasir Pengaraian Street Km 15 Rokan Hulu, Riau 
28557,. His research focuses on analisys System, Scaffolding in education, Scientific 
literacy, project-based learning. He can be contacted at email: detriamelia@rokania.ac.id. 
 
 
 
Andri Febriansyah. Currently studying at the Rokania Teaching College of Education in 
the Information Technology Education Study Program, She can be contacted at email: 
andrifeb28@gmail.com 


## Page 7

      
  
 
       ISSN: 2830-098X 
 JICTAS, Volume 1 Number 2, December 2022 : 62 -68 
68
Anik Supriani. Currently studying at the Rokania Teaching College of Education in the 
Information Technology Education Study Program, She can be contacted at email: 
putrasolo794@gmail.com, 
 
