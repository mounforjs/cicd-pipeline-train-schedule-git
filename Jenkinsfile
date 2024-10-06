pipeline {
    agent any
    stages {
        stage('Build') {
            steps {
                echo 'Running build automation'
            }
        }
        stage('DeployToProduction') {
            when {
                branch 'master'
            }
            steps {
                withCredentials([usernamePassword(credentialsId: 'webserver_login', usernameVariable: 'USERNAME', passwordVariable: 'USERPASS')]) {
                    sshPublisher(
                        failOnError: true,
                        continueOnError: false,
                        publishers: [
                            sshPublisherDesc(
                                verbose: true,
                                configName: 'production',
                                sshCredentials: [
                                    username: "$USERNAME",
                                    encryptedPassphrase: "$USERPASS"
                                ], 
                                transfers: [
                                    sshTransfer(
                                        sourceFiles: '*/',
                                        remoteDirectory: '/tmp/app',
                                        execCommand: 'sudo /usr/bin/systemctl stop apache2 && rm -rf /var/www/app/* && cp -R /tmp/app/* /var/www/app && sudo /usr/bin/systemctl start apache2 && rm -Rf /tmp/app'
                                    )
                                ]
                            )
                        ]
                    )
                }
            }
        }
    }
}
