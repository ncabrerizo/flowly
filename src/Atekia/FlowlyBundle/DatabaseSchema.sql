CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY,
  username TEXT UNIQUE,
  realname TEXT,
  password TEXT,
  role_acc INTEGER,
  role_crm INTEGER,
  role_adm INTEGER,
  active INTEGER
);