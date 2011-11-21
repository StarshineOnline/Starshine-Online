ALTER TABLE `calendrier` CHANGE `nextu` `nextu` INT( 11 ) NOT NULL DEFAULT '0' ;

insert into maree select x, y, 0, 1 from map where
   ( x = 7 and y = 418 ) or ( x BETWEEN 8 AND 22 and y = 419 ) or
   ( x BETWEEN 10 AND 22 and y = 420 ) or ( (x BETWEEN 10 AND 16 OR x BETWEEN 19 AND 23) and y = 421 ) or
   ( x BETWEEN 10 AND 22 and y = 418 ) or ( (x BETWEEN 10 AND 17 or x = 19 or x = 21 or x = 22) and y = 417 ) or
   ( (x BETWEEN 10 AND 17 or x = 19 or x = 21 or x = 22) and y = 416 ) or 
   ( (x BETWEEN 10 AND 17 or x = 19 or x = 21 or x = 22 or x = 23) and y = 415 ) or
   ( x BETWEEN 6 AND 14 and y = 422 ) or ( x BETWEEN 10 AND 13 and y = 423 ) or
   ( x BETWEEN 20 AND 22 and y = 422 ) or
   ( x BETWEEN 20 AND 24 and y = 423 ) or
   ( x BETWEEN 20 AND 25 and y = 424 ) or ( x BETWEEN 18 AND 25 and y = 425 ) or
   ( x = 17 and y = 426 ) or ( x IN (17, 21, 22, 23) and y = 427 ) or
   ( x BETWEEN 20 AND 24 and y = 426 ) or 
   ( x IN (15, 16) and y = 428 ) or ( x BETWEEN 17 AND 19 and y = 429 ) or
   ( x = 16 and y = 430 ) or ( x = 16 and y = 431 )  or
   ( x BETWEEN 11 AND 21 and y = 432 ) or ( (x BETWEEN 11 AND 21 or x = 26) and y = 433 ) or
   ( x BETWEEN 10 AND 21 and y =  434 ) or ( x BETWEEN 10 AND 23 and y = 435 ) or
   ( x BETWEEN 24 AND 26 and y =  434 ) or
   ( x BETWEEN 11 AND 21 and y = 436 ) or ( x BETWEEN 11 AND 21 and y = 437 )  or
   ( x BETWEEN 11 AND 21 and y = 438 ) or ( x = 16 and y = 439 ) or
   ( x IN (12, 21, 22) and y = 412 ) or ( x IN (12, 23)  and y = 413 ) or
   ( x IN (14, 23) and y = 414 ) or ( x = 21 and y = 411 ) or
   ( x BETWEEN 34 AND 36 and y = 410 ) or ( x BETWEEN 34 AND 36 and y = 409 ) or
   ( (x = 44 or x BETWEEN 48 AND 56) and y = 406 ) or ( x BETWEEN 44 AND 46 and y = 405 ) or
   ( x BETWEEN 48 AND 56 and y = 405 ) or
   ( x BETWEEN 46 AND 56 and y = 407 ) or ( x BETWEEN 48 AND 56 and y = 408 ) or
   ( x BETWEEN 48 AND 51 and y = 409 ) or ( x = 54 and y =  404 ) or
   ( x BETWEEN 54 AND 65 and y = 403 ) or ( x BETWEEN 58 AND 65 and y = 402 ) or
   ( x BETWEEN 58 AND 65 and y = 404 ) or ( x BETWEEN 58 AND 65 and y = 405 ) or
   ( x BETWEEN 59 AND 65 and y = 406 ) or ( x = 67 and y BETWEEN 400 AND 402 ) or
   ( x = 72 and y BETWEEN 401 AND 403 ) or ( x BETWEEN 68 AND 74 and y = 399 ) or
   ( x = 74 and y IN (398, 397) ) or ( x BETWEEN 73 AND 76 and y = 402 ) or
   ( x BETWEEN 76 AND 79 and y = 404 ) or ( x BETWEEN 76 AND 79 and y = 405 ) or
   ( x BETWEEN 76 AND 81 and y = 406 ) or ( x BETWEEN 76 AND 79 and y = 407 );
update map, maree set map.info = 101 where map.x = maree.x and map.y = maree.y and maree.zone = 1;

