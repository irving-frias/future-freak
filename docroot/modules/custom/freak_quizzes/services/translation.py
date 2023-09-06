from libretranslatepy import LibreTranslateAPI
import sys
import json

#Retrieve the JSON data from the command-line argument
if len(sys.argv) != 2:
    print("Usage: python your_script.py <json_data>")
    sys.exit(1)

json_data = sys.argv[1]

# Deserialize the JSON data into a Python dictionary
data = json.loads(json_data)
lt = LibreTranslateAPI("https://translate.argosopentech.com/")
# Access and process the data as needed
category = lt.translate(data['category'].replace('_', ' ').title(), "en", "es")
question = lt.translate(data['question']['text'].title(), "en", "es")
correct_answer = lt.translate(data['correctAnswer'].title(), "en", "es")
incorrectAnswers = list(map(lambda x: lt.translate(x.title(), "en", "es"), data['incorrectAnswers']))
tags = list(map(lambda x: lt.translate(x.replace('_', ' ').title(), "en", "es"), data['tags']))
difficulty = lt.translate(data['difficulty'].replace('_', ' ').title(), "en", "es")
type = lt.translate(data['type'].replace('_', ' ').title(), "en", "es")

# Create a JSON response object
response = {
  'category': category,
  'question': question,
  'correctAnswer': correct_answer,
  'incorrectAnswers': incorrectAnswers,
  'tags': tags,
  'difficulty': difficulty,
  'type': type
}

# Serialize the JSON response object to a string
response_json = json.dumps(response)

# Print the JSON response (this will be captured by the PHP script)
print(response_json)
