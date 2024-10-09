pipeline {
    agent any

    environment {
        def commitHash = sh(script: 'git rev-parse HEAD', returnStdout: true)
    }
    
    stages {
        stage('Build') {
          steps {
            echo 'Running build automation'
            echo "The commit hash is: ${commitHash}"
          }
        }
        stage('Build Docker Image') {
          steps {
            sh "docker version"
            sh "docker build -t aarenasjs/test ."
            sh "docker tag aarenasjs/test:latest 078407525056.dkr.ecr.us-west-1.amazonaws.com/aarenasjs/test:latest"
            sh "docker tag aarenasjs/test:latest 078407525056.dkr.ecr.us-west-1.amazonaws.com/aarenasjs/test:prod-${commitHash}"
          }
        }
        stage('Push Docker Image') {
          steps {
            sh "docker push 078407525056.dkr.ecr.us-west-1.amazonaws.com/aarenasjs/test:prod-${commitHash}"
            sh "docker system prune -af" 
          }
        }

        stage('Deploy to K8s') {
          steps{
            script {
              sh "sed -i 's,IMAGE_NAME,078407525056.dkr.ecr.us-west-1.amazonaws.com/aarenasjs/test:prod-${commitHash},' deployment.yaml"
              sh "cat deployment.yaml"
              sh "kubectl get pods"
              sh "kubectl apply -f k8s/deployment.yaml"
            }
          }
        }
    }
}
