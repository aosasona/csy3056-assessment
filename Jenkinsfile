// This Jenkinsfile handles the CI/CD pipeline for the Burrow application.
//
// Stage 1: Checkout repository
// - The latest commit from the master branch of the GitHub repository is checked out to the Jenkins server.
//
// Stage 2: Build Docker image 
// - In order to proceed to the other stages, the Docker image must be built first. The Dockerfile located in the root of the project is used for this.
//
// Stage 3: Run tests
// - The built Docker image contains the necessary dependencies to run the test suite, so an ephemeral container is created to run the tests.
//
// Stage 4: Deploy
// - The running container is stopped and removed if it exists.
//The new built image is run in detached mode, and port 8080 in the container is mapped to port 8020 on the host.

pipeline {
	agent any

	environment {
		// The output image name
		IMAGE_NAME = "trulyao/burrow"
	}

	stages {
		// Checkout the updated code from the repository
		stage("Checkout repository") {
			steps {
				git "https://github.com/aosasona/csy3056-assessment.git"
			}
		}

		// Build the Docker image
		stage("Build Docker image") {
			steps {
				// Build the Docker image using the `Dockerfile` in the repo root directory
				sh "docker build -t $IMAGE_NAME ."
			}
		}

		// Run tests
		stage("Run tests") {
			steps {
				// Execute the PHPUnit tests inside the Docker container since they have been pre-installed in the Dockerfile (build)
				sh "docker run --rm $IMAGE_NAME ./vendor/bin/phpunit --bootstrap vendor/autoload.php ./__tests__/"
			}
		}

		// Swap out the old image with the new one
		stage("Deploy") {
			steps {
				// Stop the old container if it exists
				sh "docker stop burrow || true"

				// Remove the old container if it exists
				sh "docker rm burrow || true"

				// Run the new container in detached mode, mapping port 8080 in the container to port 8020 on the host
				sh "docker run -d --name burrow -p 8020:8080 $IMAGE_NAME"
			}
		}
	}
}
