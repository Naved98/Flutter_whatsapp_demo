import 'dart:convert';
import 'package:http/http.dart' as http;

class WhatsAppService {
  final String apiUrl = 'http://localhost/twilio/msg.php'; // Replace with your PHP backend URL

  Future<bool> sendMessage(String recipient, String message) async {
    try {
      final response = await http.post(
        Uri.parse(apiUrl),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'recipient': recipient,
          'message': message,
        }),
      );

      print('Response Status: ${response.statusCode}');
      print('Response Body: ${response.body}');

      final responseData = jsonDecode(response.body);

      if (response.statusCode == 200 && responseData['success']) {
        return true;
      } else {
        print('Failed to send message: ${responseData['error']}');
        return false;
      }
    } catch (e) {
      print('Error: $e');
      return false;
    }
  }
}
