INSERT INTO USER (userId, userName, password, firstName, lastName, role) VALUES (1, "bsmith", "$2y$10$rsl.S53GJbk62jtj9DlraufVPZ.A21tXYdZOgsj3OEc3kFcTAlu1C", "Bob", "Smith", "Admin");
INSERT INTO USER (userId, userName, password, firstName, lastName, role) VALUES (2, "pjones", "$2y$10$MJgLAFnF2jlOCA3WEIyiNuZgUeMpmZiMkgX5qQuIJcu98ZVuFio8W", "Peter", "Jones", "Customer");
INSERT INTO USER (userId, userName, password, firstName, lastName, role) VALUES (3, "mjohnson", "$2y$10$OCC1j4IiZkk1DZzg/fqFnuQGp.WRr2TXuzPjmufo14ii9i1u6ORcy", "Mary", "Johnson", "Customer");
INSERT INTO USER (userId, userName, password, firstName, lastName, role) VALUES (4, "agarcia", "$2y$10$pqJQZK/BMGRvtWc2s7iQVe7zs/iQL7hFj.l00Gc.INQiNTZY6D63y", "Ana", "Garcia", "Customer");
INSERT INTO USER (userId, userName, password, firstName, lastName, role) VALUES (5, "dlee", "$2y$10$nBllMo8FuzEt6ZDSCdv2quy7PsKUnLp2gefLzGJyXA8WkKgoP4T2C", "David", "Lee", "Customer");
INSERT INTO ACCOUNT
(userId, accountType, points)
VALUES
(1, 'Standard', 5000),
(2, 'Silver', 12000),
(3, 'Gold', 25000),
(4, 'Platinum', 40000),
(5, 'Standard', 7500);

INSERT INTO GIFTCARD
(cardName, cardType, cardValue, points)
VALUES
('Amazon', 'Retail', 25.00, 2500),
('Amazon', 'Retail', 50.00, 5000),
('Amazon', 'Retail', 100.00, 10000),
('Target', 'Retail', 25.00, 2500),
('Target', 'Retail', 50.00, 7500),
('Ulta', 'Beauty', 25.00, 4000),
('Ulta', 'Beauty', 50.00, 8000),
('Walmart', 'Retail', 25.00, 2500),
('Walmart', 'Retail', 50.00, 5000),
('Starbucks', 'Food', 10.00, 1000),
('Starbucks', 'Food', 25.00, 2500),
('Best Buy', 'Electronics', 50.00, 5000),
('Best Buy', 'Electronics', 100.00, 10000),
('Apple', 'Electronics', 25.00, 2500),
('Apple', 'Electronics', 50.00, 5000),
('Nike', 'Retail', 50.00, 5000),
('Sephora', 'Beauty', 25.00, 2500),
('Sephora', 'Beauty', 50.00, 5000),
('Home Depot', 'Home Improvement', 50.00, 5000),
('DoorDash', 'Food', 25.00, 2500);
