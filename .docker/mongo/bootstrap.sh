#!/bin/bash

set -eu

# Set variables
REPLICA_USER=user
REPLICA_PASSWORD=password
REPLICA_DATABASE=database

# replicate set initiate
echo "Checking mongo container..."
until mongosh --host mongo  --eval "print(\"waited for connection\")"
do
    sleep 1
done

echo "Initializing replicaset..."
mongosh --host mongo  <<EOF
    rs.initiate(
      {
          _id: "rs0",
          version: 1,
          members: [
            { _id: 0, host: "mongo:27017"}
          ]
      }
    )
    rs.status()
EOF


echo "Creating admin user: root@root/admin"
mongosh --host mongo  <<EOF
    db.getSiblingDB('admin').createUser(
        {
            user: "root",
            pwd: "root",
            roles: [ { role: "root", db: "admin" } ]
         }
    )

    rs.status()
EOF

echo "Creating normal user: ${REPLICA_USER}:${REPLICA_PASSWORD}/${REPLICA_DATABASE}"
mongosh --host mongo  <<EOF
  use ${REPLICA_DATABASE}
  db.createUser(
    {
      user: "${REPLICA_USER}",
      pwd: "${REPLICA_PASSWORD}",
      roles: [ { role: "dbOwner", db: "${REPLICA_DATABASE}" } ]
    }
  )
EOF

echo "Confirm normal user account"
echo "---------------------------------------"
mongosh --eval 'rs.status()' "mongodb://${REPLICA_USER}:${REPLICA_PASSWORD}@mongo:27017/${REPLICA_DATABASE}"
echo "---------------------------------------"
