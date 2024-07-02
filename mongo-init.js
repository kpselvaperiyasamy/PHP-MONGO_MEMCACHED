db.createUser(
        {
            user: "selva",
            pwd: "selva123",
            roles: [
                {
                    role: "readWrite",
                    db: "gettingstarted"
                }
            ]
        }
);
