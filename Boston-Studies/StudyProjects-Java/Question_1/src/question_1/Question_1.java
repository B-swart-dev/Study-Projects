/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package question_1;
import java.util.Scanner;
/**
 *
 * @author lastp
 */
public class Question_1 {
    public static int intRecursion(int n) {
        if (n == 1) {
            return 1;
        } else { 
            return n + intRecursion(n - 1);
        }
    }
    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        Scanner input = new Scanner(System.in);
        System.out.print("Enter a positive integer to sum up using recursion: ");
        int num = input.nextInt(); // get the user-supplied number
        int sum = intRecursion(num); // call the recursive function
        System.out.println("The sum of numbers from 1 to " + num + " is " + sum);
    }
    
}
