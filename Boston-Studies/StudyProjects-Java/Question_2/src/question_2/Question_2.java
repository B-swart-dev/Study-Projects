/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package question_2;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.Scanner;
/**
 *
 * @author lastp
 */
public class Question_2 {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        ArrayList<String> stringList = new ArrayList<String>();
        Scanner scanner = new Scanner(System.in);
        String input;
        System.out.println("Enter a series of strings for your array list (enter 'stop' to stop recording):");

        while (true) {
            input = scanner.nextLine();
            if (input.equalsIgnoreCase("stop")) {
                break;
            }
            stringList.add(input);
        }
        System.out.print("You entered the following: ");
        // create an iterator for the ArrayList
        Iterator<String> iterator = stringList.iterator();

        // loop through the ArrayList using the iterator
        while (iterator.hasNext()) {
            System.out.print(iterator.next());
            if (iterator.hasNext()) {
                System.out.print(", ");
            }
        }
        System.out.println(".");
 
        }
}
