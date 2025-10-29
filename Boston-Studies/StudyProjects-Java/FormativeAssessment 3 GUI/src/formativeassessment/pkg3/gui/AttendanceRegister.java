/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package formativeassessment.pkg3.gui;

import java.beans.PropertyChangeListener;
import java.beans.PropertyChangeSupport;
import java.io.Serializable;
import javax.persistence.Basic;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.NamedQueries;
import javax.persistence.NamedQuery;
import javax.persistence.Table;
import javax.persistence.Transient;

/**
 *
 * @author lastp
 */
@Entity
@Table(name = "attendance_register", catalog = "fa3_bcar", schema = "")
@NamedQueries({
    @NamedQuery(name = "AttendanceRegister.findAll", query = "SELECT a FROM AttendanceRegister a"),
    @NamedQuery(name = "AttendanceRegister.findByName", query = "SELECT a FROM AttendanceRegister a WHERE a.name = :name"),
    @NamedQuery(name = "AttendanceRegister.findBySurname", query = "SELECT a FROM AttendanceRegister a WHERE a.surname = :surname"),
    @NamedQuery(name = "AttendanceRegister.findByEmail", query = "SELECT a FROM AttendanceRegister a WHERE a.email = :email"),
    @NamedQuery(name = "AttendanceRegister.findByPhone", query = "SELECT a FROM AttendanceRegister a WHERE a.phone = :phone"),
    @NamedQuery(name = "AttendanceRegister.findByCompany", query = "SELECT a FROM AttendanceRegister a WHERE a.company = :company")})
public class AttendanceRegister implements Serializable {

    @Transient
    private PropertyChangeSupport changeSupport = new PropertyChangeSupport(this);

    private static final long serialVersionUID = 1L;
    @Id
    @Basic(optional = false)
    @Column(name = "Name")
    private String name;
    @Basic(optional = false)
    @Column(name = "Surname")
    private String surname;
    @Basic(optional = false)
    @Column(name = "Email")
    private String email;
    @Basic(optional = false)
    @Column(name = "Phone")
    private String phone;
    @Basic(optional = false)
    @Column(name = "Company")
    private String company;

    public AttendanceRegister() {
    }

    public AttendanceRegister(String name) {
        this.name = name;
    }

    public AttendanceRegister(String name, String surname, String email, String phone, String company) {
        this.name = name;
        this.surname = surname;
        this.email = email;
        this.phone = phone;
        this.company = company;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        String oldName = this.name;
        this.name = name;
        changeSupport.firePropertyChange("name", oldName, name);
    }

    public String getSurname() {
        return surname;
    }

    public void setSurname(String surname) {
        String oldSurname = this.surname;
        this.surname = surname;
        changeSupport.firePropertyChange("surname", oldSurname, surname);
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        String oldEmail = this.email;
        this.email = email;
        changeSupport.firePropertyChange("email", oldEmail, email);
    }

    public String getPhone() {
        return phone;
    }

    public void setPhone(String phone) {
        String oldPhone = this.phone;
        this.phone = phone;
        changeSupport.firePropertyChange("phone", oldPhone, phone);
    }

    public String getCompany() {
        return company;
    }

    public void setCompany(String company) {
        String oldCompany = this.company;
        this.company = company;
        changeSupport.firePropertyChange("company", oldCompany, company);
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (name != null ? name.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof AttendanceRegister)) {
            return false;
        }
        AttendanceRegister other = (AttendanceRegister) object;
        if ((this.name == null && other.name != null) || (this.name != null && !this.name.equals(other.name))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "formativeassessment.pkg3.gui.AttendanceRegister[ name=" + name + " ]";
    }

    public void addPropertyChangeListener(PropertyChangeListener listener) {
        changeSupport.addPropertyChangeListener(listener);
    }

    public void removePropertyChangeListener(PropertyChangeListener listener) {
        changeSupport.removePropertyChangeListener(listener);
    }
    
}
