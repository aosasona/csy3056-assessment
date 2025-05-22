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
				sh "docker run --rm $IMAGE_NAME ./vendor/bin/phpunit __test_"
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
