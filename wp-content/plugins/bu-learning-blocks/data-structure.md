# Data structure for questions

## Question: True/False

```json
{
  "question": {
    "type": "true-false",
    "header": "String",
    "body": "String",
    "answers": [
      {
        "answer": "True",
        "correct": "Boolean"
      },
      {
        "answer": "False",
        "correct": "Boolean"
      }
    ],
    "feedback": {
      "correct": "String",
      "incorrect": "String"
    }
  }
}
```

### Properties

- header

  The text that goes above the question body. Default:

  ```json
  {
    "header": "Is the following statement true or false"
  }
  ```

- body

  The question body. Default:

  ```json
  {
    "body: ""
  }
  ```

- answers

  An array of 2 answers. Default:

  ```json
  {
    "answers": [
      {
        "answer": "True",
        "correct": true
      },
      {
        "answer": "False",
        "correct": false
      }
    ]
  }
  ```
- feedback

  An object with the correct and incorrect feedbacks.

  ```json
  {
    "feedback": {
      "correct": "String",
      "incorrect": "String"
    }
  }
  ```
### Examples

```json
{
  "question": {
    "type": "true-false",
    "header": "Is the following statement true or false",
    "body": "The square root of 4 is 2",
    "answers": [
      {
        "answer": "True",
        "correct": true
      },
      {
        "answer": "False",
        "correct": false
      }
    ],
    "feedback": {
      "correct": "Wow, you are like a doctor or a rocket scientist!",
      "incorrect": "Are you a kindergarden dropout?"
    }
  }
}
```

```json
{
  "question": {
    "type": "true-false",
    "header": "Is this equation right?",
    "body": "2 + 2 = 5",
    "answers": [
      {
        "answer": "Yes, that is correct",
        "correct": false
      },
      {
        "answer": "No, that equation is wrong",
        "correct": true
      }
    ],
    "feedback": {
      "correct": "Please join the elections sir, we need more people like you!",
      "incorrect": "Thanks, my whole life has been a lie."
    }
  }
}
```

## Question: Multiple Choice

```json
{
  "question": {
    "type": "multiple-choice",
    "header": "String",
    "body": "String",
    "answers": [
      {
        "answer": "String",
        "feedback": "String",
        "correct": "Boolean"
      }
    ],
    "feedback": {
      "correct": "String",
      "incorrect": "String"
    }
  }
}
```

### Examples

```json
{
  "question": {
    "type": "multiple-choice",
    "header": "Please select the correct answer",
    "body": "How many toes does a two toed sloth have?",
    "answers": [
      {
        "answer": "None",
        "feedback": "Nope, definitely more than that",
        "correct": false
      },
      {
        "answer": "Ten",
        "feedback": "Nope, that is too many",
        "correct": false
      },
      {
        "answer": "Two",
        "feedback": "Nope, a little more than that.",
        "correct": false
      },
      {
        "answer": "Either six or eight",
        "feedback": "You are correct! The name \"two-toed sloth\" erroneously describe the number of toes.",
        "correct": true
      },
      {
        "answer": "All of the above",
        "feedback": "Really?",
        "correct": false
      }
    ],
    "feedback": {
      "correct": "You picked the correct answer!",
      "incorrect": "You picked an incorrect answer, see the feedback below your answer for why it was wrong."
    }
  }
}
```

## Question: Multiple Answer

```json
{
  "question": {
    "type": "multiple-answer",
    "header": "String",
    "body": "String",
    "answers": [
      {
        "answer": "String",
        "feedback": "String",
        "correct": "Boolean"
      }
    ],
    "feedback": {
      "correct": "String",
      "incorrect": "String"
    }
  }
}
```

## Question: Matching

```json
{
  "question": {
    "type": "matching",
    "header": "String",
    "body": "String",
    "answers": [
      {
        "stimulus": "String",
        "match": "String"
      }
    ],
    "feedback": {
      "correct": "String",
      "incorrect": "String"
    }
  }
}
```

### Examples

```json
{
  "question": {
    "type": "matching",
    "header": "Match the terms",
    "body": "What are the correct portuguese translations for the following english words?",
    "answers": [
      {
        "stimulus": "Accident",
        "match": "Acidente"
      },
      {
        "stimulus": "Animal",
        "match": "Animal"
      },
      {
        "stimulus": "Continent",
        "match": "Continente"
      },
      {
        "stimulus": "Telephone",
        "match": "Telefone"
      },
      {
        "stimulus": "Gorilla",
        "match": "Gorila"
      }
    ],
    "feedback": {
      "correct": "Not so hard to speak portuguese huh?",
      "incorrect": "Not so hard to speak portuguese huh?"
    }
  }
}
```

## Question: Calculated Numeric

```json
{
  "question": {
    "type": "calculated-numeric",
    "header": "String",
    "body": "String",
    "answer": "Float",
    "answerRange": "Float",
    "decimalPlaces": "Integer",
    "feedback": {
      "correct": "String",
      "incorrect": "String"
    }
  }
}
```

### Examples

```json
{
  "question": {
    "type": "calculated-numeric",
    "header": "What is the result of",
    "body": "10 / 3",
    "answer": 1.333,
    "answerRange": 0.01,
    "decimalPlaces": 3,
    "feedback": {
      "correct": "The accepted answer range is 1.323 to 1.343",
      "incorrect": "The accepted answer range is 1.323 to 1.343"
    }
  }
}
```

## Question: Fill in the Blank

```json
{
  "question": {
    "type": "fill-in-the-blank",
    "header": "String",
    "body": "String",
    "answer": "String",
    "caseSensitive": "Boolean",
    "feedback": {
      "correct": "String",
      "incorrect": "String"
    }
  }
}
```

### Fill in the Blank Example

``` json
{
  "question": {
    "type": "fill-in-the-blank",
    "header": "Type in the word exactly in lower case",
    "body": "What is the Spanish word for grandmother?",
    "answer": "abuela",
    "caseSensitive": true,
    "feedback": {
      "correct": "Yes, that is correct",
      "incorrect": "No, that is not the exact lower case spelling",
    }
  }
}
```
